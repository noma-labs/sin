<?php

declare(strict_types=1);

namespace App\Nomadelfia\AggiornamentoAnagrafe\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

final class AggiornamentoAnagrafe extends Activity
{
    public const LOG_NAME = 'nomadelfia';

    public const EVENT_POPOLAZIONE_ENTER = 'popolazione.entrata';

    public const EVENT_POPOLAZIONE_EXIT = 'popolazione.uscita';

    public const EVENT_POPOLAZIONE_DEATH = 'popolazione.decesso';

    protected function scopeEnter(Builder $query): void
    {
        $query->where('event', self::EVENT_POPOLAZIONE_ENTER);
    }

    protected function scopeExit(Builder $query): void
    {
        $query->where('event', self::EVENT_POPOLAZIONE_EXIT);
    }

    protected function scopeDeath(Builder $query): void
    {
        $query->where('event', self::EVENT_POPOLAZIONE_DEATH);

    }

    public function isEnterEvent(): bool
    {
        return $this->event === self::EVENT_POPOLAZIONE_ENTER;
    }

    public function isExitEvent(): bool
    {
        return $this->event === self::EVENT_POPOLAZIONE_EXIT;

    }

    public function isDeathEvent(): bool
    {
        return $this->event === self::EVENT_POPOLAZIONE_DEATH;
    }

    protected static function booted(): void
    {
        self::addGlobalScope('nomadelfia', function (Builder $builder): void {
            $builder->where('log_name', self::LOG_NAME);
        });
    }
}
