<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Patente\Models\CQC;
use App\Patente\Models\Patente;
use Illuminate\Http\Request;

final class PatenteCQCController
{
    public function update(Request $request, $numero)
    {
        $patente = Patente::findorfail($numero);

        $syncData = [];
        foreach ($request->cqc as $cqcId => $data) {
            if (! array_key_exists('id', $data) || empty($data['id'])) {
                continue; // Skip this iteration if the id key is not present or is empty
            }
            $syncData[$cqcId] = [];
            $syncData[$cqcId]['data_scadenza'] = null;
            $syncData[$cqcId]['data_rilascio'] = null;
            if (! empty($data['data_scadenza'])) {
                $syncData[$cqcId]['data_scadenza'] = $data['data_scadenza'];
            }
            if (! empty($data['data_rilascio'])) {
                $syncData[$cqcId]['data_rilascio'] = $data['data_rilascio'];
            }
        }
        $patente->cqc()->detach(CQC::all()->pluck('id'));
        $patente->cqc()->attach($syncData);

        return redirect(route('patente.visualizza', ['numero' => $numero]))->withSuccess('CQC aggiornate con successo.');
    }
}
