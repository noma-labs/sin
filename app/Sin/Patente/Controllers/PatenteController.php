<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Patente\Models\CategoriaPatente;
use App\Patente\Models\CQC;
use App\Patente\Models\Patente;
use Illuminate\Http\Request;

final class PatenteController
{
    public function index()
    {
        $patenti = Patente::with('persona')->SenzaCommisione()->InScadenza(config('patente.scadenze.patenti.inscadenza'))->orderBy('data_scadenza_patente')->get(); // 45 giorni
        $patentiScadute = Patente::with('persona')->SenzaCommisione()->Scadute(config('patente.scadenze.patenti.scadute'))->orderBy('data_scadenza_patente', 'asc')->get();

        $patentiCQCPersone = CQC::CQCPersone()->inScadenza(config('patente.scadenze.cqc.inscadenza'))->with('persona')->orderBy('data_scadenza', 'asc')->get();
        $patentiCQCPersoneScadute = CQC::CQCPersone()->scadute(config('patente.scadenze.cqc.scadute'))->with('persona')->orderBy('data_scadenza', 'asc')->get();
        $patentiCQCMerci = CQC::CQCMerci()->inScadenza(config('patente.scadenze.cqc.inscadenza'))->with('persona')->orderBy('data_scadenza', 'asc')->get();
        $patentiCQCMerciScadute = CQC::CQCMerci()->scadute(config('patente.scadenze.cqc.scadute'))->with('persona')->orderBy('data_scadenza', 'asc')->get();

        $patentiCommissione = Patente::with('persona')->ConCommisione()->InScadenza(config('patente.scadenze.commissione.inscadenza'))->orderBy('data_scadenza_patente')->get();
        $patentiCommisioneScadute = Patente::with('persona')->ConCommisione()->Scadute(config('patente.scadenze.commissione.scadute'))->orderBy('data_scadenza_patente', 'asc')->get();

        $patentiAll = Patente::sortable()->NonScadute()->with('persona')->orderBy('data_scadenza_patente', 'asc')->paginate(50);

        return view('patente.scadenze', compact('patenti',
            'patentiScadute',
            'patentiCQCPersone',
            'patentiCQCPersoneScadute',
            'patentiCQCMerci',
            'patentiCQCMerciScadute',
            'patentiCommissione',
            'patentiCommisioneScadute',
            'patentiAll'
        ));
    }

    public function create()
    {
        $categorie = CategoriaPatente::orderby('categoria')->get();
        $cqcs = CQC::orderby('categoria')->get();

        return view('patente.create', compact('categorie', 'cqcs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'persona_id' => 'required',
            'numero_patente' => 'required',
            'data_rilascio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after:data_rilascio_patente',
            'rilasciata_dal' => 'required',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'numero_patente.required' => 'Il numero patente è obbligatorio.',
            'data_rilascio_patente.required' => 'La data di rilascio è obbligatoria.',
            'data_scadenza_patente.required' => 'La data di scadenza è obbligatoria.',
            'rilasciata_dal.required' => "L'ente che ha rilasciato è obbligatorio.",
        ]);

        $patente = new Patente();
        $patente->persona_id = $request->input('persona_id');
        $patente->numero_patente = $request->input('numero_patente');
        $patente->data_rilascio_patente = $request->input('data_rilascio_patente');
        $patente->data_scadenza_patente = $request->input('data_scadenza_patente');
        $patente->rilasciata_dal = $request->input('rilasciata_dal');
        $patente->note = $request->input('note');
        $patente->stato = $request->input('assegnaCommissione') === 'on' ? 'commissione' : null;
        $patente->save();

        $patente->refresh();

        $patente->categorie()->attach($request->input('categorie'));

        $syncData = [];
        foreach ($request->cqc as $cqcId => $data) {
            if (empty($data['id'])) {
                continue;
            }
            if (! empty($data['data_scadenza'])) {
                $syncData[$cqcId]['data_scadenza'] = $data['data_scadenza'];
            }
            if (! empty($data['data_rilascio'])) {
                $syncData[$cqcId]['data_rilascio'] = $data['data_rilascio'];
            }
        }

        $patente->cqc()->attach($syncData);

        return redirect(route('patente.scadenze'))->withSuccess('Patente inserita con successo.');
    }

    public function show(string $numero)
    {
        $categorie = CategoriaPatente::all();
        $cqcs = CQC::all();
        $patente = Patente::with('categorie', 'cqc')->findorfail($numero);

        return view('patente.show', compact('categorie', 'cqcs', 'patente'));
    }

    public function update(Request $request, string $numero)
    {
        $request->validate([
            'numero_patente' => 'required',
            'data_rilascio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after:data_rilascio_patente',
            'rilasciata_dal' => 'required',
        ], [
            'numero_patente.required' => 'Il numero patente è obbligatorio.',
            'data_rilascio_patente.required' => 'La data di rilascio è obbligatoria.',
            'data_scadenza_patente.required' => 'La data di scadenza è obbligatoria.',
            'rilasciata_dal.required' => "L'ente che ha rilasciato è obbligatorio.",
        ]);

        $patente = Patente::findorfail($numero);
        $patente->numero_patente = $request->input('numero_patente');
        $patente->data_rilascio_patente = $request->input('data_rilascio_patente');
        $patente->data_scadenza_patente = $request->input('data_scadenza_patente');
        $patente->rilasciata_dal = $request->input('rilasciata_dal');
        $patente->note = $request->input('note');
        $patente->stato = $request->input('assegnaCommissione') === 'on' ? 'commissione' : null;
        $patente->save();

        return redirect(route('patente.visualizza', ['numero' => $patente->numero_patente]))->withSuccess('Patente aggiornata con successo.');
    }

    public function delete($numero)
    {
        Patente::destroy($numero);

        return redirect()->route('patente.scadenze')->withSuccess('Patente eliminata con successo.');
    }
}
