<?php

declare(strict_types=1);

// API route (/api/biblioteca)
Route::group(['prefix' => 'biblioteca', 'namespace' => 'App\Biblioteca\Controllers'], function () {
    Route::get('/collocazione', 'ApiController@autocompleteCollocazione')->name('api.biblioteca.collocazione');
});
