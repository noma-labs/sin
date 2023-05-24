<?php

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Core\Controllers\BaseController as CoreBaseController;
use Carbon;
use Domain\Nomadelfia\EserciziSpirituali\Models\EserciziSpirituali;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Actions\ExportPopolazioneToWordAction;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use Spatie\Activitylog\Models\Activity;

class PersonaPopolazioneController extends CoreBaseController
{
    public function index($idPersona){
        $persona = Persona::findOrFail($idPersona);

        $attuale = PopolazioneNomadelfia::where('persona_id', $idPersona)->whereNull('data_uscita')->first();
        $storico = PopolazioneNomadelfia::where('persona_id', $idPersona)->whereNotNull('data_uscita')->orderby('data_entrata')->get();

        return view('nomadelfia.persone.popolazione.show', compact('persona', 'attuale', 'storico'));
    }
}
