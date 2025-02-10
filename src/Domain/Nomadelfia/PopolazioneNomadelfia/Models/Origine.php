<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Models;

enum Origine: string
{
    case Interno = 'interno';
    case Accolto = 'accolto';
    case MinorenneConFamiglia = 'famiglia';
    case Esterno = 'esterno';
}
