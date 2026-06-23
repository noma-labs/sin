<?php

declare(strict_types=1);

namespace App\Providers;

use App\Ai\Providers\TransformersProvider;
use App\Biblioteca\Models\Libro;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Ai\AiManager;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureLaravel8upgrade();
        $this->configureModels();
        $this->configureBookDeleted();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->resolving(AiManager::class, function (AiManager $manager, $app): void {
            $manager->extend('transformers', fn ($app, array $config) => new TransformersProvider(
                $config,
                $app->make(Dispatcher::class),
            ));
        });
    }

    /**
     * Configure the application's models.
     */
    private function configureLaravel8upgrade(): void
    {
        // laravel 8: requires this in order to use boostrap as default paginator (and not tailwind)
        // https://laravel.com/docs/8.x/upgrade#pagination-defaults
        Paginator::useBootstrap();
    }

    /**
     * Configure the application's models.
     */
    private function configureModels(): void
    {
        // FIXME: use the Model::shouldBeStrict();
        Model::preventSilentlyDiscardingAttributes();
        Model::preventAccessingMissingAttributes();

        Model::unguard();
    }

    private function configureBookDeleted(): void
    {
        // every time a book is deleted, save the collocation in the deleted_note and remove from printed
        Libro::deleted(function ($libro): void {
            $libro->deleted_note = "$libro->collocazione - $libro->deleted_note";
            $libro->collocazione = '';
            $libro->tobe_printed = 0; // remove from the list of the libri to be printed
            $libro->save();
        });
    }
}
