<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
final class JoinLeaveHistoryController
{
    #[Middleware('can:popolazione.persona.visualizza')]
    public function index($id)
    {
        $persona = Persona::findOrFail($id);

        $attuale = PopolazioneNomadelfia::where('persona_id', $id)->whereNull('data_uscita')->first();
        $storico = PopolazioneNomadelfia::where('persona_id', $id)->whereNotNull('data_uscita')->orderby('data_entrata')->get();

        return view('nomadelfia.persone.popolazione.show', compact('persona', 'attuale', 'storico'));
    }
}
