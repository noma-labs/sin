<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * The famiglie of Maria Bianchi, to show every case of the albero familiare (anagrafe/people/{id}/families):
 *
 * - the nonni paterni registered, the genitori of the madre not registered (Aggiungi genitori);
 * - genitori deceased, with seven figli: Maria, two fratelli married (one as moglie in the famiglia of the marito),
 *   a fratello accolto in the famiglia after being born in another one (more famiglie d'origine), vedovo and married
 *   again (two coniugi), a sorella adult without famiglia (Nuova famiglia), a fratello celibe di elezione;
 * - Maria with two unioni: a marito deceased, without famiglia d'origine, then a famiglia of mamma di vocazione with
 *   70 figli: in the nucleo, aggregati, gone out who formed a famiglia, gone out without one, a deceased one, and
 *   figli accolti in more famiglie (the famiglia before and the one after);
 * - Luca Esposito, born outside, accolto by Maria and then by Paolo: three famiglie d'origine.
 */
final class AlberoFamiliareSeeder extends Seeder
{
    public function run(): void
    {
        $nonno = $this->persona('Pietro', 'Bianchi', 'M', '1915-02-11', '1990-07-02');
        $nonna = $this->persona('Rosa', 'Bianchi', 'F', '1918-05-23', '1999-11-30');
        $famigliaDeiNonni = $this->coniugi('Bianchi Pietro', '1938-09-10', $nonno, $nonna, stato: '0');

        $padre = $this->persona('Giuseppe', 'Bianchi', 'M', '1940-03-15', '2010-01-20');
        $famigliaDeiNonni->assegnaFiglioNato($padre, stato: '0');
        $madre = $this->persona('Anna', 'Ferri', 'F', '1944-08-02', '2015-04-12');
        $origine = $this->coniugi('Bianchi Giuseppe', '1963-06-01', $padre, $madre, stato: '0');

        $maria = $this->persona('Maria', 'Bianchi', 'F', '1964-04-14');
        $origine->assegnaFiglioNato($maria, stato: '0');
        $this->famiglieDiMaria($maria);

        $paolo = $this->persona('Paolo', 'Bianchi', 'M', '1966-09-03');
        $origine->assegnaFiglioNato($paolo, stato: '0');
        $famigliaDiPaolo = $this->coniugi('Bianchi Paolo', '1990-05-12', $paolo, $this->persona('Laura', 'Neri', 'F', '1968-01-25'));
        $famigliaDiPaolo->assegnaFiglioNato($this->persona('Francesco', 'Bianchi', 'M', '1992-02-17'), stato: '0');
        $famigliaDiPaolo->assegnaFiglioNato($this->persona('Giulia', 'Bianchi', 'F', '1995-10-08'), stato: '0');
        $famigliaDiPaolo->assegnaFiglioNato($this->persona('Tommaso', 'Bianchi', 'M', '2012-06-21'));

        $lucia = $this->persona('Lucia', 'Bianchi', 'F', '1968-12-01');
        $origine->assegnaFiglioNato($lucia, stato: '0');
        $famigliaDiLucia = $this->coniugi('Gallo Carlo', '1992-09-19', $this->persona('Carlo', 'Gallo', 'M', '1965-03-30'), $lucia);
        $famigliaDiLucia->assegnaFiglioNato($this->persona('Sofia', 'Gallo', 'F', '1994-07-14'), stato: '0');
        $famigliaDiLucia->assegnaFiglioNato($this->persona('Matteo', 'Gallo', 'M', '1997-11-02'), stato: '0');

        $giovanni = $this->persona('Giovanni', 'Bianchi', 'M', '1970-02-09');
        $famigliaDiNascita = $this->coniugi('Rossi Mario', '1965-04-24', $this->persona('Mario', 'Rossi', 'M', '1938-10-10', '2001-02-14'), $this->persona('Elsa', 'Rossi', 'F', '1941-06-06', '2008-09-09'), stato: '0');
        $famigliaDiNascita->assegnaFiglioNato($giovanni, stato: '0');
        $origine->assegnaFiglioAccolto($giovanni, stato: '0', dataEntrata: '1975-09-01');
        $primoMatrimonio = $this->coniugi('Bianchi Giovanni', '1995-06-17', $giovanni, $this->persona('Sara', 'Moretti', 'F', '1972-05-05', '2005-03-18'), stato: '0');
        $primoMatrimonio->assegnaFiglioNato($this->persona('Alessio', 'Bianchi', 'M', '1998-08-30'), stato: '0');
        $secondoMatrimonio = $this->coniugi('Bianchi Giovanni E Carla', '2008-05-24', $giovanni, $this->persona('Carla', 'Leone', 'F', '1975-12-12'));
        $secondoMatrimonio->assegnaFiglioNato($this->persona('Noemi', 'Bianchi', 'F', '2010-04-01'));

        $teresa = $this->persona('Teresa', 'Bianchi', 'F', '1972-07-27');
        $origine->assegnaFiglioNato($teresa, stato: '0');

        $marco = $this->persona('Marco', 'Bianchi', 'M', '1975-01-18');
        $origine->assegnaFiglioNato($marco, stato: '0');
        $this->famiglia('Bianchi Marco', TipoFamiglia::CelibeDiElezione, '2005-10-04')->assegnaCapoFamiglia($marco);

        $luca = $this->persona('Luca', 'Esposito', 'M', '1997-03-09');
        $this->figlioInPiuFamiglie($luca, $famigliaDiPaolo);
    }

    /**
     * Maria married a marito without famiglia d'origine, deceased, then started a famiglia of mamma di vocazione.
     */
    private function famiglieDiMaria(Persona $maria): void
    {
        $marito = $this->persona('Luigi', 'Conti', 'M', '1960-11-11', '1990-02-03');
        $matrimonio = $this->coniugi('Conti Luigi', '1985-05-04', $marito, $maria, stato: '0');
        $matrimonio->assegnaFiglioNato($this->persona('Elena', 'Conti', 'F', '1987-09-22'), stato: '0');

        $mammaDiVocazione = $this->famiglia('Bianchi Maria', TipoFamiglia::MammaDiVocazione, '1992-03-01');
        $mammaDiVocazione->assegnaCapoFamiglia($maria);

        for ($i = 0; $i < 12; $i++) {
            $nascita = Carbon::create(2008 + $i % 12, 1 + $i % 12, 5 + $i);
            $mammaDiVocazione->assegnaFiglioAccolto($this->figlio($nascita), dataEntrata: $nascita->copy()->addYears(2)->toDateString());
        }

        for ($i = 0; $i < 3; $i++) {
            $mammaDiVocazione->assegnaAggregato(
                $this->figlio(Carbon::create(1955 + $i * 6, 6, 1)),
                stato: $i === 0 ? '1' : '0',
                dataEntrata: Carbon::create(1998 + $i * 7, 1, 15)->toDateString(),
            );
        }

        for ($i = 0; $i < 20; $i++) {
            $nascita = Carbon::create(1980 + intdiv($i, 2), 1 + $i % 12, 10);
            $figlio = $this->figlio($nascita);
            $mammaDiVocazione->assegnaFiglioAccolto($figlio, stato: '0', dataEntrata: $nascita->copy()->addYears(12)->toDateString());
            $this->famigliaPropria($figlio, $nascita->copy()->addYears(26));
        }

        for ($i = 0; $i < 30; $i++) {
            $nascita = Carbon::create(1985 + intdiv($i, 2), 1 + $i % 12, 20);
            $mammaDiVocazione->assegnaFiglioAccolto(
                $this->figlio($nascita, decesso: $i === 7 ? '2019-08-14' : null),
                stato: '0',
                dataEntrata: $nascita->copy()->addYears(7)->toDateString(),
            );
        }

        // figli born in another famiglia: two of them still with Maria, two accolti afterwards in a third famiglia
        for ($i = 0; $i < 4; $i++) {
            $nascita = Carbon::create(1999 + $i, 4, 3);
            $padre = $this->figlio(Carbon::create(1970 + $i, 2, 2), sesso: 'M');
            $nascitaAltrove = $this->coniugi($this->nomeFamiglia($padre), $nascita->copy()->subYears(3)->toDateString(), $padre, $this->figlio(Carbon::create(1972 + $i, 3, 3), sesso: 'F'), stato: '0');
            $sesso = $i % 2 === 0 ? 'M' : 'F';
            $figlio = $this->persona($sesso === 'M' ? fake()->firstNameMale() : fake()->firstNameFemale(), $padre->cognome, $sesso, $nascita->toDateString());
            $nascitaAltrove->assegnaFiglioNato($figlio, stato: '0');
            $mammaDiVocazione->assegnaFiglioAccolto($figlio, stato: $i < 2 ? '1' : '0', dataEntrata: $nascita->copy()->addYears(5)->toDateString());
            if ($i >= 2) {
                $capofamiglia = $this->figlio(Carbon::create(1962 + $i, 5, 5), sesso: 'M');
                $successiva = $this->coniugi($this->nomeFamiglia($capofamiglia), '1990-01-01', $capofamiglia, $this->figlio(Carbon::create(1964 + $i, 6, 6), sesso: 'F'));
                $successiva->assegnaFiglioAccolto($figlio, dataEntrata: $nascita->copy()->addYears(12)->toDateString());
            }
        }
    }

    /**
     * The name of a famiglia after its capofamiglia, made unique against the random names of the other famiglie.
     */
    private function nomeFamiglia(Persona $capofamiglia, ?int $anno = null): string
    {
        $nome = mb_trim($capofamiglia->cognome.' '.$capofamiglia->nome.' '.($anno ?? ''));

        return Famiglia::query()->where('nome_famiglia', $nome)->exists() ? $nome.' '.$capofamiglia->id : $nome;
    }

    /**
     * Luca was born in a famiglia outside Nomadelfia, accolto by Maria, then accolto in the famiglia of Paolo: three
     * famiglie d'origine for him, the famiglia before and the one after among the figli of Maria.
     */
    private function figlioInPiuFamiglie(Persona $luca, Famiglia $famigliaDiPaolo): void
    {
        $nascita = $this->coniugi('Esposito Antonio', '1994-02-12', $this->persona('Antonio', 'Esposito', 'M', '1970-09-09'), $this->persona('Giovanna', 'Esposito', 'F', '1973-03-03'), stato: '0');
        $nascita->assegnaFiglioNato($luca, stato: '0');
        Famiglia::query()->where('nome_famiglia', 'Bianchi Maria')->firstOrFail()->assegnaFiglioAccolto($luca, stato: '0', dataEntrata: '2003-09-01');
        $famigliaDiPaolo->assegnaFiglioAccolto($luca, dataEntrata: '2009-09-01');
    }

    /**
     * A figlio gone out who married: a famiglia of coniugi with a coniuge of the right sesso.
     */
    private function famigliaPropria(Persona $figlio, Carbon $matrimonio): void
    {
        $coniuge = $this->figlio(Carbon::create($matrimonio->year - 27, 9, 9), sesso: $figlio->isMaschio() ? 'F' : 'M');
        [$marito, $moglie] = $figlio->isMaschio() ? [$figlio, $coniuge] : [$coniuge, $figlio];
        $this->coniugi($this->nomeFamiglia($marito, $matrimonio->year), $matrimonio->toDateString(), $marito, $moglie);
    }

    private function figlio(Carbon $nascita, ?string $sesso = null, ?string $decesso = null): Persona
    {
        $sesso ??= fake()->boolean() ? 'M' : 'F';
        $nome = $sesso === 'M' ? fake()->firstNameMale() : fake()->firstNameFemale();
        $cognome = fake()->lastName();

        return $this->persona($nome, $cognome, $sesso, $nascita->toDateString(), $decesso);
    }

    private function persona(string $nome, string $cognome, string $sesso, string $nascita, ?string $decesso = null): Persona
    {
        return Persona::factory()->create([
            'nome' => $nome,
            'cognome' => $cognome,
            'nominativo' => $nome.' '.$cognome,
            'sesso' => $sesso,
            'data_nascita' => $nascita,
            'data_decesso' => $decesso,
        ]);
    }

    private function famiglia(string $nome, TipoFamiglia $tipo, string $dataCreazione): Famiglia
    {
        return Famiglia::factory()->create(['nome_famiglia' => $nome, 'tipo' => $tipo, 'data_creazione' => $dataCreazione]);
    }

    /**
     * @param  string  $stato  '0' for a famiglia of the storico, e.g. with the coniugi deceased
     */
    private function coniugi(string $nome, string $dataCreazione, Persona $marito, Persona $moglie, string $stato = '1'): Famiglia
    {
        $famiglia = $this->famiglia($nome, TipoFamiglia::Coniugi, $dataCreazione);
        $famiglia->assegnaCapoFamiglia($marito, stato: $stato);
        $famiglia->assegnaMoglie($moglie, $stato);

        return $famiglia;
    }
}
