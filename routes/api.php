<?php

declare(strict_types=1);

// API route: /api/officina/
Route::group(['prefix' => 'officina', 'namespace' => 'App\Officina\Controllers'], function () {
    Route::post('gomme/elimina', 'ApiController@eliminaGomma')->name('api.officina.gomme.elimina');
    Route::post('gomme/nuova', 'ApiController@nuovaGomma')->name('api.officina.gomme.nuova');
    Route::get('gomme/', 'ApiController@gomme')->name('api.officina.gomme');
    Route::get('filtri/tipi', 'ApiController@tipiFiltro')->name('api.officina.filtri.tipi');
    Route::delete('filtri/elimina', 'ApiController@eliminaFiltro')->name('api.officina.filtri.elimina');
    Route::get('filtri/', 'ApiController@filtri')->name('api.officina.filtri');
});

// API route (/api/biblioteca)
Route::group(['prefix' => 'biblioteca', 'namespace' => 'App\Biblioteca\Controllers'], function () {
    Route::get('/collocazione', 'ApiController@autocompleteCollocazione')->name('api.biblioteca.collocazione');
});

//Api route /api/patente
Route::group(['prefix' => 'patente', 'namespace' => 'App\Patente\Controllers'], function () {
    Route::get('/persone', 'ApiController@persone')->name('api.patente.persone');
    Route::get('/rilasciata', 'ApiController@rilasciata')->name('api.patente.rilascio');
    Route::get('/persone/senzapatente', 'ApiController@personeSenzaPatente')->name('api.patente.persone.senzapatente');
    Route::get('/categorie', 'ApiController@categorie')->name('api.patente.categorie');
    Route::get('/cqc', 'ApiController@cqc')->name('api.patente.cqc');
    Route::get('/{numero}', 'ApiController@patente')->name('api.patente');
    Route::put('/{numero}', 'ApiController@update')->name('api.patente.modifica');
});
