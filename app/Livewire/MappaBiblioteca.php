<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Biblioteca\ArgomentiBiblioteca;
use App\Biblioteca\Collocazione;
use App\Biblioteca\Models\Libro;
use App\Biblioteca\Models\Video;
use App\Biblioteca\Piantina;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use stdClass;

final class MappaBiblioteca extends Component
{
    public const AREA_ELABORATI = 'scuola';

    private const LUNGHEZZA_MINIMA_RICERCA = 3;

    private const ELABORATI_MINIMI_PER_COLONNA_SECONDARIA = 3;

    public string $cerca = '';

    public ?string $area = null;

    public ?string $argomentoElaborato = null;

    public ?string $colonna = null;

    public function updatedCerca(): void
    {
        $this->reset('area', 'argomentoElaborato');

        $codice = $this->colonnaDellaCollocazione($this->cerca);
        if ($codice !== null) {
            $this->colonna = $codice;
        }
    }

    public function selezionaColonna(string $codice): void
    {
        if (array_key_exists($codice, ArgomentiBiblioteca::COLONNE)) {
            $this->colonna = $codice;
        }
    }

    public function selezionaArea(string $area): void
    {
        if (! array_key_exists($area, ArgomentiBiblioteca::AREE)) {
            return;
        }

        $this->area = $this->area === $area ? null : $area;
        $this->reset('cerca', 'argomentoElaborato');
    }

    public function selezionaArgomentoElaborato(string $argomento): void
    {
        if ($this->area !== self::AREA_ELABORATI || ! array_key_exists($argomento, ArgomentiBiblioteca::ARGOMENTI_ELABORATI)) {
            return;
        }

        if ($this->argomentoElaborato === $argomento) {
            $this->argomentoElaborato = null;

            return;
        }

        $this->argomentoElaborato = $argomento;
        $this->colonna = ArgomentiBiblioteca::ARGOMENTI_ELABORATI[$argomento]['colonne'][0];
    }

    /**
     * Libri e ultimo numero usato per ogni colonna, calcolati dalle collocazioni del catalogo.
     *
     * @return array<string, array{libri: int, ultimoNumero: int, ripiani: array<string, int>}>
     */
    #[Computed]
    public function statistiche(): array
    {
        $statistiche = [];
        $righe = Libro::query()
            ->toBase()
            ->selectRaw('UPPER(SUBSTRING(collocazione, 1, 3)) AS lettere, COUNT(*) AS libri, MAX(CAST(SUBSTRING(collocazione, 4) AS UNSIGNED)) AS ultimo_numero')
            ->whereRaw("collocazione REGEXP '^[A-Za-z]{3}[0-9]{1,3}$'")
            ->groupBy('lettere')
            ->get();

        foreach ($righe as $riga) {
            $collocazione = Collocazione::parse($riga->lettere);
            if ($collocazione === null) {
                continue;
            }

            $colonna = $statistiche[$collocazione->colonna] ?? ['libri' => 0, 'ultimoNumero' => 0, 'ripiani' => []];
            $colonna['libri'] += (int) ($riga->libri);
            $colonna['ultimoNumero'] = max($colonna['ultimoNumero'], (int) ($riga->ultimo_numero));
            if ($collocazione->ripiano !== null) {
                $colonna['ripiani'][$collocazione->ripiano] = (int) ($riga->libri);
                ksort($colonna['ripiani']);
            }
            $statistiche[$collocazione->colonna] = $colonna;
        }

        return $statistiche;
    }

    /**
     * Prima collocazione libera dopo l'ultima usata nella colonna (es. "SPB058").
     */
    public function prossimaCollocazione(string $colonna): string
    {
        $ultimoNumero = $this->statistiche[$colonna]['ultimoNumero'] ?? 0;

        return $colonna.mb_str_pad((string) ($ultimoNumero + 1), 3, '0', STR_PAD_LEFT);
    }

    public function render(): View
    {
        [$evidenziate, $secondarie] = $this->colonneEvidenziate();

        return view('livewire.mappa-biblioteca', [
            'viewBox' => Piantina::VIEW_BOX,
            'forme' => Piantina::COLONNE,
            'colonne' => ArgomentiBiblioteca::COLONNE,
            'aree' => ArgomentiBiblioteca::AREE,
            'argomentiElaborati' => $this->argomentiElaboratiConConteggio(),
            'evidenziate' => $evidenziate,
            'secondarie' => $secondarie,
            'classificazioni' => $this->classificazioniDellaColonna(),
            'sigleVideo' => $this->sigleVideoDellaColonna(),
        ]);
    }

    /**
     * Colonne da evidenziare (null quando non c'è nessun filtro) e colonne secondarie, mostrate attenuate.
     *
     * @return array{0: list<string>|null, 1: list<string>}
     */
    private function colonneEvidenziate(): array
    {
        if ($this->argomentoElaborato !== null) {
            $consigliate = ArgomentiBiblioteca::ARGOMENTI_ELABORATI[$this->argomentoElaborato]['colonne'];
            $secondarie = array_values(collect(ArgomentiBiblioteca::ELABORATI_PER_COLONNA)
                ->map(fn (array $argomenti): int => $argomenti[$this->argomentoElaborato] ?? 0)
                ->filter(fn (int $elaborati): bool => $elaborati >= self::ELABORATI_MINIMI_PER_COLONNA_SECONDARIA)
                ->except($consigliate)
                ->sortDesc()
                ->keys()
                ->all());

            return [$consigliate, $secondarie];
        }

        if ($this->area !== null) {
            $colonne = array_keys(array_filter(ArgomentiBiblioteca::COLONNE, fn (array $colonna): bool => $colonna['area'] === $this->area));

            return [$colonne, []];
        }

        $codice = $this->colonnaDellaCollocazione($this->cerca);
        if ($codice !== null) {
            return [[$codice], []];
        }

        $testo = $this->normalizza($this->cerca);
        if (mb_strlen($testo) < self::LUNGHEZZA_MINIMA_RICERCA) {
            return [null, []];
        }

        $colonne = array_keys(array_filter(
            ArgomentiBiblioteca::COLONNE,
            fn (array $colonna): bool => str_contains(
                $this->normalizza($colonna['argomento'].' '.$colonna['dettaglio'].' '.ArgomentiBiblioteca::AREE[$colonna['area']]),
                $testo,
            ),
        ));

        return [$colonne, []];
    }

    /**
     * Colonna indicata da una collocazione (es. "DGB039", "SPA012") o da un codice colonna scritto in maiuscolo (es. "DG").
     * Un testo minuscolo senza numeri è trattato come ricerca per argomento, così "ma" non seleziona la colonna MA.
     */
    private function colonnaDellaCollocazione(string $testo): ?string
    {
        $testo = str_replace(' ', '', $testo);
        if (preg_match('/\d/', $testo) !== 1 && $testo !== mb_strtoupper($testo)) {
            return null;
        }

        $testo = mb_strtoupper($testo);
        if (array_key_exists($testo, ArgomentiBiblioteca::COLONNE)) {
            return $testo;
        }

        $collocazione = Collocazione::parse($testo);
        if ($collocazione === null || ! array_key_exists($collocazione->colonna, ArgomentiBiblioteca::COLONNE)) {
            return null;
        }

        return $collocazione->colonna;
    }

    private function normalizza(string $testo): string
    {
        return Str::of($testo)->ascii()->lower()->replace("'", '')->squish()->value();
    }

    /**
     * @return array<string, array{nome: string, descrizione: string, colonne: list<string>, elaborati: int}>
     */
    private function argomentiElaboratiConConteggio(): array
    {
        return collect(ArgomentiBiblioteca::ARGOMENTI_ELABORATI)
            ->map(fn (array $argomento, string $chiave): array => $argomento + [
                'elaborati' => collect(ArgomentiBiblioteca::ELABORATI_PER_COLONNA)->sum(fn (array $argomenti): int => $argomenti[$chiave] ?? 0),
            ])
            ->all();
    }

    /**
     * Classificazioni più frequenti tra i libri della colonna selezionata.
     *
     * @return array<string, int>
     */
    private function classificazioniDellaColonna(): array
    {
        if ($this->colonna === null || str_starts_with($this->colonna, 'Z-')) {
            return [];
        }

        return Libro::query()
            ->toBase()
            ->join('classificazione', 'classificazione.id', '=', 'libro.classificazione_id')
            ->where('libro.collocazione', 'like', $this->colonna.'%')
            ->selectRaw('classificazione.descrizione AS descrizione, COUNT(*) AS libri')
            ->groupBy('classificazione.descrizione')
            ->orderByDesc('libri')
            ->limit(3)
            ->get()
            ->mapWithKeys(fn (stdClass $riga): array => [$riga->descrizione => (int) ($riga->libri)])
            ->all();
    }

    /**
     * Video per sigla nel riquadro Z selezionato: DVD e CD hanno le sigle che iniziano per Z, le videocassette le altre.
     *
     * @return array<string, int>
     */
    private function sigleVideoDellaColonna(): array
    {
        if ($this->colonna !== 'Z-DVD' && $this->colonna !== 'Z-VHS') {
            return [];
        }

        $operatore = $this->colonna === 'Z-DVD' ? 'like' : 'not like';

        return Video::query()
            ->toBase()
            ->selectRaw("UPPER(SUBSTRING_INDEX(TRIM(cassetta), ' ', 1)) AS sigla, COUNT(*) AS video")
            ->where('cassetta', $operatore, 'Z%')
            ->groupBy('sigla')
            ->orderByDesc('video')
            ->get()
            ->mapWithKeys(fn (stdClass $riga): array => [$riga->sigla => (int) ($riga->video)])
            ->all();
    }
}
