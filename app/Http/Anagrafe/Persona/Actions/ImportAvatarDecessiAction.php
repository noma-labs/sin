<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Actions;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Imports the photos of the deceased, kept on the media_originals disk in one folder per month of the decesso
 * ("DECESSI1 GENNAIO" … "DECESSI12 DICEMBRE"), as the avatar of the persone.
 *
 * The file names are loose (the nome or the nominativo, sometimes a surname initial, the dates or a photo code), so the import has two steps:
 * suggerimenti() suggests the persona of each photo for a CSV to review by hand, then execute() imports the reviewed CSV.
 * Many photos are crops of a tagged group photo: their XMP tags keep the names FOTO of the file ALFA di Enrico
 * (alfa_enrico_15_feb_23.FOTO, linked to the persona by persone.id_alfa_enrico), which identify the persona better than the file name.
 */
final readonly class ImportAvatarDecessiAction
{
    public const DISCO = 'media_originals';

    /**
     * The columns of the CSV of the suggestions, in order.
     */
    public const COLONNE_SUGGERIMENTI = ['file', 'mese', 'nome_file', 'tag', 'persona_id', 'candidati', 'esito'];

    /**
     * The columns of the CSV read by the import.
     */
    public const COLONNE = ['file', 'persona_id', 'esito'];

    /**
     * The esito of the rows to import, set by the suggestion when one persona matches or by hand: the other esiti are skipped.
     */
    public const ESITO_DA_IMPORTARE = 'OK';

    public const ESITO_AMBIGUO = 'AMBIGUO';

    public const ESITO_NON_TROVATO = 'NON_TROVATO';

    private const ESTENSIONI = ['jpg', 'jpeg', 'png'];

    private const DATA = '/(\d{1,2})\.(\d{1,2})\.(\d{4})/';

    /**
     * The titles before the nome in the file names: "Don Aldo", "P. Bernardo", "Padre Andrea", "Mamma Nina".
     */
    private const TITOLI = ['don', 'p', 'padre', 'suor', 'mamma'];

    public function __construct(private SalvaAvatarPersonaAction $salvaAvatar) {}

    /**
     * Suggests the persona of each photo in $cartella: the deceased with a name FOTO in the tags of the photo, otherwise
     * the deceased in the month of its folder whose name matches the file name.
     *
     * @return list<array{file: string, mese: string, nome_file: string, tag: string, persona_id: string, candidati: string, esito: string}>
     */
    public function suggerimenti(string $cartella): array
    {
        $deceduti = Persona::query()->where('data_decesso', '!=', null)->get();
        $decedutiPerNomeFoto = $this->decedutiPerNomeFoto($deceduti);

        $righe = [];
        foreach ($this->disco()->allFiles($cartella) as $file) {
            if (! in_array(mb_strtolower(pathinfo($file, PATHINFO_EXTENSION)), self::ESTENSIONI, true)) {
                continue;
            }
            $mese = $this->mese($file);
            $nomeFile = $this->analizzaNomeFile(basename($file));
            $tag = $this->nomiNeiTag($this->disco()->path($file));
            [$candidati, $contraddetti] = $this->candidatiDaiTag($decedutiPerNomeFoto, $tag, $mese, $nomeFile);
            if ($candidati->isEmpty()) {
                [$candidati, $contraddetti] = $this->candidati($deceduti, $mese, $nomeFile);
            }
            $esito = match (true) {
                $candidati->isEmpty() => self::ESITO_NON_TROVATO,
                $candidati->count() === 1 && ! $contraddetti => self::ESITO_DA_IMPORTARE,
                default => self::ESITO_AMBIGUO,
            };

            $righe[] = [
                'file' => $file,
                'mese' => $mese === null ? '' : (string) $mese,
                'nome_file' => mb_trim($nomeFile['nome'].' '.($nomeFile['iniziale'] ?? '')),
                'tag' => implode(' | ', $tag),
                'persona_id' => $esito === self::ESITO_DA_IMPORTARE ? (string) $candidati->first()->id : '',
                'candidati' => $candidati->map($this->descriviCandidato(...))->implode(' | '),
                'esito' => $esito,
            ];
        }

        usort($righe, fn (array $a, array $b): int => [(int) $a['mese'], $a['file']] <=> [(int) $b['mese'], $b['file']]);

        return $this->segnalaPersoneRipetute($righe);
    }

    /**
     * Saves the photos of the rows to import as the avatar of their persona; with $salva false nothing is saved, to check the file.
     *
     * @param  list<array<string, string>>  $righe  the rows of the reviewed CSV, by column name
     * @return array{importati: int, ignorati: int}
     *
     * @throws RuntimeException listing the problems of the file; nothing is saved
     */
    public function execute(array $righe, bool $salva = true, bool $sovrascrivi = false): array
    {
        $problemi = $this->problemi($righe, $sovrascrivi);
        if ($problemi !== []) {
            throw new RuntimeException("Importazione interrotta, nessun avatar è stato salvato. Correggi il file e rilancia:\n".implode("\n", $problemi));
        }

        $daImportare = array_filter($righe, $this->isDaImportare(...));
        if ($salva) {
            $persone = Persona::query()->findMany(array_map(fn (array $riga): int => (int) $riga['persona_id'], $daImportare))->keyBy('id');
            foreach ($daImportare as $riga) {
                $this->salvaAvatar->execute($persone[(int) $riga['persona_id']], $this->disco()->path($riga['file']));
            }
        }

        return [
            'importati' => count($daImportare),
            'ignorati' => count($righe) - count($daImportare),
        ];
    }

    /**
     * @param  list<array<string, string>>  $righe
     * @return list<string> one line per problem, with the line of the CSV (the header is line 1)
     */
    public function problemi(array $righe, bool $sovrascrivi = false): array
    {
        $daImportare = array_filter($righe, $this->isDaImportare(...));
        $persone = Persona::query()
            ->findMany(array_map(fn (array $riga): int => (int) $riga['persona_id'], array_values($daImportare)))
            ->keyBy('id');

        $problemi = [];
        $righePersona = [];
        foreach ($daImportare as $indice => $riga) {
            $linea = 'riga '.($indice + 2);
            $persona = ctype_digit($riga['persona_id']) ? $persone->get((int) $riga['persona_id']) : null;
            if (! $persona instanceof Persona) {
                $problemi[] = "- {$linea}: la persona `{$riga['persona_id']}` non esiste";
            } elseif (! $persona->isDeceduta()) {
                $problemi[] = "- {$linea}: la persona `{$riga['persona_id']}` non è deceduta";
            } elseif ($persona->avatar_path !== null && ! $sovrascrivi) {
                $problemi[] = "- {$linea}: la persona `{$riga['persona_id']}` ha già un avatar (usa --force per sostituirlo)";
            } elseif (isset($righePersona[$persona->id])) {
                $problemi[] = "- {$linea}: la persona `{$riga['persona_id']}` è già nella riga {$righePersona[$persona->id]}";
            }
            if ($persona instanceof Persona) {
                $righePersona[$persona->id] ??= $indice + 2;
            }

            if (! $this->disco()->exists($riga['file'])) {
                $problemi[] = "- {$linea}: il file `{$riga['file']}` non esiste";
            } elseif (@getimagesize($this->disco()->path($riga['file'])) === false) {
                $problemi[] = "- {$linea}: il file `{$riga['file']}` non è un'immagine";
            }
        }

        return $problemi;
    }

    /**
     * Reads the name of a photo: drops the extension, the dates, the photo codes, the copy numbers and the "Don" title.
     * "Anna S 44105-02.jpg" gives the nome "anna" and the surname initial "s"; "ALDO 08.05.1960 - 12.04.2021.jpg"
     * gives the nome "aldo" with the dates of nascita and decesso.
     *
     * @return array{nome: string, iniziale: string|null, nascita: string|null, decesso: string|null} the nome in lower case without accents
     */
    public function analizzaNomeFile(string $nomeFile): array
    {
        $testo = pathinfo($nomeFile, PATHINFO_FILENAME);

        preg_match_all(self::DATA, $testo, $trovate, PREG_SET_ORDER);
        $date = array_map(fn (array $data): string => sprintf('%04d-%02d-%02d', $data[3], $data[2], $data[1]), $trovate);
        [$nascita, $decesso] = match (count($date)) {
            0 => [null, null],
            1 => [null, $date[0]],
            default => [$date[0], $date[1]],
        };

        $testo = (string) preg_replace([
            '/(\d{1,2})\.(\d{1,2})\.(\d{4})[a-z]?/', // the dates, with a letter typed after them
            '/_modificato-\d+/i',
            '/\(\d+\)/',
            '/IMG_/',
            '/(?:LB|VG|S)?\d[\w-]*/',   // photo codes: "46028-12", "32507a18", "S2729", "LB1196-30", "VG0099-05", "Giuseppe3"
            '/([a-z])([A-Z])(?=\s|$)/', // a surname initial glued to the nome: "AntonioS"
            '/[_\-.()]/',
        ], [' ', ' ', ' ', ' ', ' ', '$1 $2', ' '], $testo);

        $parole = preg_split('/\s+/', mb_strtolower(Str::ascii($testo)), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($parole) > 1 && in_array($parole[0], self::TITOLI, true)) {
            array_shift($parole);
        }
        $iniziale = count($parole) > 1 && mb_strlen((string) end($parole)) === 1 ? array_pop($parole) : null;
        $nome = implode(' ', $parole);

        return [
            'nome' => mb_strlen($nome) > 1 ? $nome : '',
            'iniziale' => $iniziale,
            'nascita' => $nascita,
            'decesso' => $decesso,
        ];
    }

    /**
     * The names of the persone in the XMP tags of a photo (dc:subject, PersonInImage and the names of the face regions),
     * read from the file without exiftool. A crop of a group photo keeps the names of everybody in the original photo.
     *
     * @return list<string>
     */
    public function nomiNeiTag(string $percorsoFoto): array
    {
        $contenuto = @file_get_contents($percorsoFoto);
        if ($contenuto === false || preg_match('/<x:xmpmeta.*?<\/x:xmpmeta>/s', $contenuto, $xmp) !== 1) {
            return [];
        }

        $nomi = [];
        foreach (['dc:subject', 'Iptc4xmpExt:PersonInImage'] as $elemento) {
            if (preg_match("/<{$elemento}>\s*<rdf:Bag>(.*?)<\/rdf:Bag>/s", $xmp[0], $bag) === 1) {
                preg_match_all('/<rdf:li>(.*?)<\/rdf:li>/s', $bag[1], $voci);
                array_push($nomi, ...$voci[1]);
            }
        }
        preg_match_all('/mwg-rs:Name="([^"]*)"/', $xmp[0], $regioni);
        array_push($nomi, ...$regioni[1]);

        $nomi = array_map(fn (string $nome): string => (string) preg_replace('/\s+/', ' ', mb_trim(html_entity_decode($nome, ENT_QUOTES | ENT_XML1))), $nomi);

        return array_values(array_unique(array_filter($nomi, fn (string $nome): bool => $nome !== '')));
    }

    /**
     * The deceased with a name FOTO among the tags of the photo, in the month of its folder. When the photo has more names
     * (a crop of a group photo), the names sharing a word with the file name come first ("Andrea.jpg" picks "ANDREA AL").
     * A month that would leave no candidate is ignored and reported as contradicted.
     *
     * @param  array<string, list<Persona>>  $decedutiPerNomeFoto
     * @param  list<string>  $tag
     * @param  array{nome: string, iniziale: string|null, nascita: string|null, decesso: string|null}  $nomeFile
     * @return array{0: Collection<int, Persona>, 1: bool} the candidates, and whether the month was contradicted
     */
    private function candidatiDaiTag(array $decedutiPerNomeFoto, array $tag, ?int $mese, array $nomeFile): array
    {
        $paroleFile = explode(' ', $nomeFile['nome']);
        $tagDelFile = array_filter($tag, fn (string $nome): bool => array_intersect(explode(' ', $this->normalizza(str_replace('.', ' ', $nome))), $paroleFile) !== []);

        $taggati = [];
        foreach ($tagDelFile !== [] ? $tagDelFile : $tag as $nome) {
            array_push($taggati, ...$decedutiPerNomeFoto[$this->normalizza($nome)] ?? []);
        }
        $candidati = collect($taggati)->unique('id')->values();

        $nelMese = $candidati->filter(fn (Persona $persona): bool => $mese === null || Date::parse($persona->data_decesso)->month === $mese)->values();
        if ($nelMese->isEmpty() && $candidati->isNotEmpty()) {
            return [$candidati, true];
        }

        return [$nelMese, false];
    }

    /**
     * The deceased by their name FOTO of the file ALFA di Enrico, normalized: more than one persona can share a name.
     *
     * @param  Collection<int, Persona>  $deceduti
     * @return array<string, list<Persona>>
     */
    private function decedutiPerNomeFoto(Collection $deceduti): array
    {
        $decedutiPerIdAlfa = $deceduti->filter(fn (Persona $persona): bool => $persona->id_alfa_enrico !== null)->groupBy('id_alfa_enrico');
        $nomiFoto = DB::connection('db_nomadelfia')->table('alfa_enrico_15_feb_23')
            ->whereIn('ID', $decedutiPerIdAlfa->keys())
            ->where('FOTO', '<>', '')
            ->pluck('FOTO', 'ID');

        $perNome = [];
        foreach ($nomiFoto as $id => $nomeFoto) {
            if (! is_string($nomeFoto)) {
                continue;
            }
            foreach ($decedutiPerIdAlfa->get($id, []) as $persona) {
                $perNome[$this->normalizza($nomeFoto)][] = $persona;
            }
        }

        return $perNome;
    }

    /**
     * The deceased in $mese (any month when null) whose nome, nominativo or nome and cognome match the name of the photo,
     * narrowed by the surname initial and the dates of the file name. A hint that would leave no candidate is ignored,
     * and reported as contradicted so the photo is not imported without a review.
     *
     * @param  Collection<int, Persona>  $deceduti
     * @param  array{nome: string, iniziale: string|null, nascita: string|null, decesso: string|null}  $nomeFile
     * @return array{0: Collection<int, Persona>, 1: bool} the candidates, and whether a hint was contradicted
     */
    private function candidati(Collection $deceduti, ?int $mese, array $nomeFile): array
    {
        $nome = $nomeFile['nome'];
        $iniziale = $nomeFile['iniziale'];
        if ($nome === '') {
            return [collect(), false];
        }

        $candidati = $deceduti
            ->filter(fn (Persona $persona): bool => $mese === null || Date::parse($persona->data_decesso)->month === $mese)
            ->filter(function (Persona $persona) use ($nome, $iniziale): bool {
                $nomePersona = $this->normalizza($persona->nome);
                $nominativo = $this->normalizza($persona->nominativo);
                $nomeCognome = $this->normalizza($persona->nome.' '.$persona->cognome);

                return in_array($nome, [$nomePersona, $nominativo, $nomeCognome], true)
                    || ($iniziale !== null && $nominativo === "{$nome} {$iniziale}")
                    || str_starts_with($nominativo, "{$nome} ")
                    || str_starts_with($nomePersona, "{$nome} ")
                    || str_starts_with($nome, "{$nomeCognome} ");
            })
            ->values();

        $contraddetti = false;
        $indizi = [
            fn (Persona $persona): bool => $iniziale === null
                || str_starts_with($this->normalizza($persona->cognome), $iniziale)
                || $this->normalizza($persona->nominativo) === "{$nome} {$iniziale}",
            fn (Persona $persona): bool => $nomeFile['decesso'] === null || Date::parse($persona->data_decesso)->toDateString() === $nomeFile['decesso'],
            fn (Persona $persona): bool => $nomeFile['nascita'] === null || ($persona->data_nascita && Date::parse($persona->data_nascita)->toDateString() === $nomeFile['nascita']),
        ];
        foreach ($indizi as $indizio) {
            $ristretti = $candidati->filter($indizio)->values();
            if ($ristretti->isEmpty() && $candidati->isNotEmpty()) {
                $contraddetti = true;
            } else {
                $candidati = $ristretti;
            }
        }

        return [$candidati, $contraddetti];
    }

    /**
     * A persona suggested for more than one photo ("Giovanni.jpg" and "Giovanni (2).jpg") is left to choose by hand.
     *
     * @param  list<array{file: string, mese: string, nome_file: string, tag: string, persona_id: string, candidati: string, esito: string}>  $righe
     * @return list<array{file: string, mese: string, nome_file: string, tag: string, persona_id: string, candidati: string, esito: string}>
     */
    private function segnalaPersoneRipetute(array $righe): array
    {
        $ripetute = array_filter(array_count_values(array_filter(array_column($righe, 'persona_id'))), fn (int $volte): bool => $volte > 1);

        return array_map(fn (array $riga): array => isset($ripetute[$riga['persona_id']])
            ? array_merge($riga, ['persona_id' => '', 'esito' => self::ESITO_AMBIGUO])
            : $riga, $righe);
    }

    /**
     * The month of the decesso from the folder of the photo ("DECESSI4 APRILE" gives 4), null when the folder has no month.
     */
    private function mese(string $file): ?int
    {
        if (preg_match('/DECESSI\s*(\d{1,2})\b/i', basename(dirname($file)), $trovato) !== 1) {
            return null;
        }
        $mese = (int) $trovato[1];

        return $mese >= 1 && $mese <= 12 ? $mese : null;
    }

    private function descriviCandidato(Persona $persona): string
    {
        $nascita = $persona->data_nascita ? Date::parse($persona->data_nascita)->year : '?';
        $decesso = Date::parse($persona->data_decesso)->toDateString();

        return "{$persona->id}:{$persona->nome} {$persona->cognome} ({$nascita}–{$decesso})";
    }

    private function normalizza(?string $testo): string
    {
        return (string) preg_replace('/\s+/', ' ', mb_trim(mb_strtolower(Str::ascii((string) $testo))));
    }

    /**
     * @param  array<string, string>  $riga
     */
    private function isDaImportare(array $riga): bool
    {
        return $riga['esito'] === self::ESITO_DA_IMPORTARE;
    }

    private function disco(): Filesystem
    {
        return Storage::disk(self::DISCO);
    }
}
