<?php

declare(strict_types=1);

namespace App\Nomadelfia\Famiglia\Controllers;

use App\Nomadelfia\Famiglia\Models\Famiglia;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\UscitaFamigliaAction;
use Carbon\Carbon;
use Illuminate\Http\Request;

final class FamilyLeaveController
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'data_uscita' => ['required', 'date'],
        ], [
            'data_uscita.required' => 'La data di uscita è obbligatoria.',
            'data_uscita.date' => 'La data di uscita non è una data corretta.',
        ]);
        $famiglia = Famiglia::findorfail($id);
        $action = resolve(UscitaFamigliaAction::class);
        $action->execute($famiglia, \Illuminate\Support\Facades\Date::parse($request->data_uscita));

        return redirect(route('nomadelfia.families.show', $id))->withSuccess('Famiglia uscita con successo.');
    }
}
