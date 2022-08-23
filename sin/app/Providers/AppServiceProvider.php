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
