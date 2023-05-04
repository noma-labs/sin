<?php

namespace App\Providers;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
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

        Blade::if('maggiorenne', function ($persona) {
            return Carbon::now()->subYears(18)->toDatestring() > $persona->data_nascita;
        });

        // which should be an instance of DateTime
        Blade::directive('year', function ($date) {
            return "<?php echo Carbon::parse($date)->year ?>";
        });

        // Directoves that return the number of days from the $date.
        Blade::directive('diffdays', function ($date) {
            return "<?php echo Carbon::now()->diffInDays(Carbon::parse($date)) ?>";
        });

        // Directoves that return the number of days from the $date.
        Blade::directive('diffYears', function ($date) {
            return "<?php echo Carbon::now()->diffInYears(Carbon::parse($date)) ?>";
        });

        Blade::directive('diffHumans', function ($date) {
            return "<?php echo Carbon::parse($date)->diffForHumans(['parts' => 3, 'join' => true]) ?>";
        });

        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('hasrole', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endhasrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('hasanyrole', function ($arguments) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$arguments})): ?>";
        });
        Blade::directive('endhasanyrole', function () {
            return '<?php endif; ?>';
        });
    }

    public function register()
    {

    }
}
