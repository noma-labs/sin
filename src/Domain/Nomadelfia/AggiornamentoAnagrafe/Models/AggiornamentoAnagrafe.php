<?php

namespace Domain\Nomadelfia\AggiornamentoAnagrafe\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class AggiornamentoAnagrafe extends Activity
{
    const LOG_NAME = 'nomadelfia';

    const EVENT_POPOLAZIONE_ENTER = 'popolazione.entrata';

    const EVENT_POPOLAZIONE_EXIT = 'popolazione.uscita';

    const EVENT_POPOLAZIONE_DEATH = 'popolazione.decesso';

    protected static function booted(): void
    {
        static::addGlobalScope('nomadelfia', function (Builder $builder) {
            $builder->where('log_name', self::LOG_NAME);
        });
    }

    public function scopeEnter(Builder $query): void
    {
        $query->where('event', 'popolazione.entrata');
    }

    public function scopeExit(Builder $query): void
    {
        $query->where('event', 'popolazione.uscita');
    }

    public function scopeDeath(Builder $query): void
    {
        $query->where('event', 'popolazione.decesso');
    }
}
