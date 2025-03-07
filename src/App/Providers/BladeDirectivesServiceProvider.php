<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class BladeDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Return True of the persona live in the "centro di spirito".
        Blade::if('liveRome', function ($persona) {
            if (is_string($persona)) {
                $persona = Persona::findOrFail($persona);
            }
            $gruppoAttuale = $persona->gruppofamiliareAttuale();
            if (! $gruppoAttuale) {
                return false;
            }

            return $persona->gruppofamiliareAttuale()->isCentroDiSpirito();
        });

        Blade::if('maggiorenne', function ($persona): bool {
            return Carbon::now()->subYears(18)->toDatestring() > $persona->data_nascita;
        });

        // which should be an instance of DateTime
        Blade::directive('year', function ($date): string {
            return "<?php echo Carbon::parse($date)->year ?>";
        });

        // Directoves that return the number of days from the $date.
        Blade::directive('diffdays', function ($date): string {
            return "<?php echo Carbon::now()->diffInDays(Carbon::parse($date)) ?>";
        });

        // Directoves that return the number of days from the $date.
        Blade::directive('diffYears', function ($date): string {
            return "<?php echo Carbon::now()->diffInYears(Carbon::parse($date), true) ?>";
        });

        Blade::directive('diffHumans', function ($date): string {
            return "<?php echo Carbon::parse($date)->diffForHumans(now(), Carbon\CarbonInterface::DIFF_ABSOLUTE) ?>";
        });

        Blade::directive('role', function ($role): string {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function (): string {
            return '<?php endif; ?>';
        });

        Blade::directive('hasrole', function ($role): string {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endhasrole', function (): string {
            return '<?php endif; ?>';
        });

        Blade::directive('hasanyrole', function ($arguments): string {
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$arguments})): ?>";
        });
        Blade::directive('endhasanyrole', function (): string {
            return '<?php endif; ?>';
        });
    }

    public function register(): void {}
}
