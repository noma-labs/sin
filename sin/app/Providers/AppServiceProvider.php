<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Blade;

use App\Biblioteca\Models\Libro;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
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
        Blade::directive('role', function ($role) {
            // dd(auth()->check() && auth()->user()->hasRole($role));
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('hasrole', function ($role) {
            // list($role, $guard) = explode(',', $arguments.',');
            return "<?php if(auth()->check() && auth()->user()->hasRole({$role})): ?>";
        });
     
        Blade::directive('endhasrole', function () {
            return '<?php endif; ?>';
        });

        Blade::directive('hasanyrole', function ($arguments) {
        //     dd($arguments);
        //     list($roles) = explode('|', $arguments.'|');
        //    dd($roles);
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$arguments})): ?>";
        });
        Blade::directive('endhasanyrole', function () {
            return '<?php endif; ?>';
        });
    }
}
