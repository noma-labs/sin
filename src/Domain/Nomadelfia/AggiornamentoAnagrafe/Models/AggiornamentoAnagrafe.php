<?php

namespace Domain\Nomadelfia\AggiornamentoAnagrafe\Models;

use Spatie\Activitylog\Models\Activity;

class AggiornamentoAnagrafe extends Activity
{
    public static function entrati()
    {
        return self::inLog('nomadelfia')->ForEvent('popolazione.entrata')->orderBy('created_at', 'DESC')->take(20)->get();
    }

    public static function LastMonth()
    {
        return self::inLog('nomadelfia')->ForEvent('popolazione.entrata')->orderBy('created_at', 'DESC')->take(20)->get();
    }
}
