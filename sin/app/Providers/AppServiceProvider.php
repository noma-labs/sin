<?php

namespace App\Providers;
use Illuminate\Pagination\Paginator;

use App\Nomadelfia\Models\Persona;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Blade;

use App\Biblioteca\Models\Libro;
use Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        # laravel 8: requires this in order to use boostrap as default paginator (and not tailwind)
        # https://laravel.com/docs/8.x/upgrade#pagination-defaults
        Paginator::useBootstrap();

        // Return True of the persona live in the centro di spirito.
        Blade::if('liveRome', function ($persona) {
            if (is_string($persona)) {
                $persona= Persona::findOrFail($persona);
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
                return "<?php echo Carbon::parse($date)->diffForHumans() ?>";
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

        // Resolve error: Syntax error or access violation: 1071 Specified key was too long; max key length is 767 bytes
        Schema::defaultStringLength(191);

        //salva la collocazione nelle note ogni volta che viene cancellato il libro
        Libro::deleted(function ($libro) {
            #Salva la collocazione vecchia nelle note
            $libro->deleted_note = "$libro->collocazione - $libro->deleted_note";
            $libro->collocazione = "";
            $libro->tobe_printed = 0; // remove from the list of the libri to be printed
            $libro->save();
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
