<?php

namespace App\Providers;

use App\Biblioteca\Models\Libro;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // laravel 8: requires this in order to use boostrap as default paginator (and not tailwind)
        // https://laravel.com/docs/8.x/upgrade#pagination-defaults
        Paginator::useBootstrap();

        //salva la collocazione nelle note ogni volta che viene cancellato il libro
        Libro::deleted(function ($libro): void {
            //Salva la collocazione vecchia nelle note
            $libro->deleted_note = "$libro->collocazione - $libro->deleted_note";
            $libro->collocazione = '';
            $libro->tobe_printed = 0; // remove from the list of the libri to be printed
            $libro->save();
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {}
}
