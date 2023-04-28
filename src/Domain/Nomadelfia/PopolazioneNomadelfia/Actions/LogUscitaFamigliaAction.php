<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Actions;

use Domain\Nomadelfia\PopolazioneNomadelfia\DataTransferObjects\UscitaFamigliaData;

class LogUscitaFamigliaAction
{
    public function execute(UscitaFamigliaData $dto)
    {
        $comp = $dto->componenti->map(function ($persona, $key) {
            return [
                'nominativo' => $persona->nominativo,
                'luogo_nascita' => $persona->provincia_nascita,
                'data_nascita' => $persona->data_nascita,
                'data_entrata' => $persona->getDataEntrataNomadelfia() ?: '',
                'numero_elenco' => $persona->numero_elenco,
            ];
        });
        activity('nomadelfia')
            ->performedOn($dto->famiglia)
            ->withProperties([
                'data_uscita' => $dto->data_uscita,
                'componenti' => $comp,
            ]
            )
            ->setEvent('popolazione.uscita-famiglia')
            ->log('Famiglia uscita in data :properties.data_uscita');

    }
}
