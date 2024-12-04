<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;
use Domain\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaFamigliaAction;
use Exception;
use Illuminate\Http\Request;

final class FamiglieController
{
    public function view()
    {
        $capifamiglieMaschio = Famiglia::onlyCapoFamiglia()->maschio();
        $capifamiglieFemmina = Famiglia::onlyCapoFamiglia()->femmina();

        $singleMaschio = Famiglia::single()->maschio();
        $singleFemmine = Famiglia::single()->femmina();

        $famigliaError = Famiglia::famigliaConErrore();

        return view('nomadelfia.famiglie.index',
            compact('capifamiglieMaschio', 'capifamiglieFemmina', 'singleMaschio', 'singleFemmine', 'famigliaError'));
    }

    public function show(Request $request, $id)
    {
        $famiglia = Famiglia::findorfail($id);
        $componenti = $famiglia->mycomponenti();
        $gruppoAttuale = $famiglia->gruppoFamiliareAttuale();
        $gruppiStorici = $famiglia->gruppiFamiliariStorico();

        return view('nomadelfia.famiglie.show', compact('famiglia', 'componenti', 'gruppoAttuale', 'gruppiStorici'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome_famiglia' => 'required',
            'data_creazione' => 'required|date',
        ], [
            'nome_famiglia.required' => 'Il nome della fmaiglia è obbligatorio.',
            'data_creazione.required' => 'La data di creazione della famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);

        $famiglia->nome_famiglia = $request->nome_famiglia;
        $famiglia->data_creazione = $request->data_creazione;
        $saved = $famiglia->save();
        if ($saved) {
            return redirect(route('nomadelfia.famiglia.dettaglio',
                ['id' => $id]))->withSuccess("Famiglia $famiglia->nome_famiglia aggiornata con successo");
        }

        return redirect(route('nomadelfia.famiglia.dettaglio',
            ['id' => $id]))->withErrors("Errore. Famiglia $famiglia->nome_famiglia non aggioranta");

    }

    public function eliminaGruppoFamiliare(Request $request, $id, $idGruppo)
    {
        $famiglia = Famiglia::findorfail($id);
        $gruppo = GruppoFamiliare::find($idGruppo);
        $famiglia->rimuoviDaGruppoFamiliare($idGruppo);

        return redirect(route('nomadelfia.gruppifamiliari',
            ['id' => $idGruppo]))->withSuccess("Famiglia $famiglia->nome_famiglia eliminatada $gruppo->nome con successo");
    }

    /**
     * Sposta la famiglia e tutti i componenti attivi in un nuovo gruppo familiare.
     *
     * @author Davide Neri
     **/
    public function spostaInGruppoFamiliare(Request $request, $id)
    {
        $request->validate([
            'nuovo_gruppo_id' => 'required',
            'data_cambiogruppo' => 'required|date',
        ], [
            'nuovo_gruppo_id.required' => 'Il nuovo gruppo dove spostare la famiglia è obbligatorio',
            'data_cambiogruppo.required' => 'La data del cambio di gruppo è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        $gruppo_corrente = $famiglia->gruppoFamiliareAttualeOrFail();
        $famiglia->assegnaFamigliaANuovoGruppoFamiliare($gruppo_corrente->id, $request->data_cambiogruppo,
            $request->nuovo_gruppo_id, $request->data_cambiogruppo);

        return redirect(route('nomadelfia.famiglia.dettaglio',
            ['id' => $id]))->withSuccess('Famiglia spostata nel gruppo familiare con successo');

    }

    public function assegnaComponente(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
            'posizione' => 'required',
            'stato' => 'required',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'stato.required' => 'Lo stato della persona è obbligatoria.',
            'posizione.required' => 'La posizione nella famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        $persona = Persona::findorfail($request->persona_id);

        switch ($request->posizione) {
            case 'CAPO FAMIGLIA':
                $famiglia->assegnaCapoFamiglia($persona);
                break;
            case 'MOGLIE':
                $famiglia->assegnaMoglie($persona);
                break;
            case 'FIGLIO NATO':
                $famiglia->assegnaFiglioNato($persona);
                break;
            case 'FIGLIO ACCOLTO':
                $famiglia->assegnaFiglioAccolto($persona);
                break;
            default:
                return redirect(route('nomadelfia.famiglia.dettaglio',
                    ['id' => $id]))->withErrors("Posizione `{$request->posizione}` non riconosciuta");
        }

        return redirect(route('nomadelfia.famiglia.dettaglio',
            ['id' => $id]))->withSuccess("$persona->nominativo aggiunto alla famiglia $famiglia->nome_famiglia con successo");

    }

    public function aggiornaComponente(Request $request, $id)
    {
        $request->validate([
            'persona_id' => 'required',
            'posizione' => 'required',
            'stato' => 'required',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'stato.required' => 'Lo stato della persona è obbligatoria.',
            'posizione.required' => 'La posizione nella famiglia è obbligatoria.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        try {
            $famiglia->componenti()->updateExistingPivot($request->persona_id, [
                'stato' => $request->stato,
                'posizione_famiglia' => $request->posizione,
                'note' => $request->note,
            ]);

            return redirect(route('nomadelfia.famiglia.dettaglio',
                ['id' => $id]))->withSuccess('Componente aggiornato con successo');
        } catch (Exception) {
            return redirect(route('nomadelfia.famiglia.dettaglio',
                ['id' => $id]))->withError('Errore. Nessun componente aggiornato alla famiglia.');
        }
    }

    public function uscita(Request $request, $id)
    {
        $request->validate([
            'data_uscita' => 'required|date',
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria.',
            'data_uscita.date' => 'La data di uscita non è una data corretta.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        $action = app(UscitaFamigliaAction::class);
        $action->execute($famiglia, $request->data_uscita);

        return redirect(route('nomadelfia.famiglia.dettaglio',
            ['id' => $id]))->withSuccess('Famiglia uscita con successo.');
    }
}
