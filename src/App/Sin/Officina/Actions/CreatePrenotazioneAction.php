<?php

namespace App\Officina\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;
use App\Officina\Models\Prenotazioni;
use App\Officina\Models\Uso;
use App\Officina\Models\Veicolo;

class CreatePrenotazioneAction
{

    public function __invoke(
        Persona $cliente,
        Veicolo $veicolo,
        Persona $meccanico,
        string $data_partenza,
        string $data_arrivo,
        string $ora_partenza,
        string $ora_arrivo,
        Uso $uso,
        string $note,
        string $destianazione
    ): Prenotazioni {

        return Prenotazioni::create([
            'cliente_id' => $cliente->id,
            'veicolo_id' => $veicolo->id,
            'meccanico_id' => $meccanico->id,
            'data_partenza' => $data_partenza,
            'ora_partenza' =>  $ora_partenza,
            'data_arrivo' => $data_arrivo,
            'ora_arrivo' => $ora_arrivo,
            'uso_id' => $uso->ofus_iden,
            'note' => $note,
            'destinazione' => $destianazione
        ]);
    }


}