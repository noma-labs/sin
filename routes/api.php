<?php

declare(strict_types=1);

use App\Nomadelfia\Api\Controllers\ApiController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
// });

// API route: /api/officina/
Route::group(['prefix' => 'officina', 'namespace' => 'App\Officina\Controllers'], function () {
    Route::get('tipologia', 'ApiController@tipologia')->name('api.officina.tipologia');
    Route::get('alimentazione', 'ApiController@alimentazione')->name('api.officina.alimentazione');
    Route::get('impiego', 'ApiController@impiego')->name('api.officina.impiego');
    Route::get('marche', 'ApiController@marche')->name('api.officina.marche');
    Route::get('clienti', 'ApiController@clientiMeccanica')->name('api.officina.clienti');
    Route::get('veicoli', 'ApiController@veicoli')->name('api.officina.veicoli');
    Route::post('gomme/elimina', 'ApiController@eliminaGomma')->name('api.officina.gomme.elimina');
    Route::post('gomme/nuova', 'ApiController@nuovaGomma')->name('api.officina.gomme.nuova');
    Route::get('gomme/', 'ApiController@gomme')->name('api.officina.gomme');
    Route::get('filtri/tipi', 'ApiController@tipiFiltro')->name('api.officina.filtri.tipi');
    Route::delete('filtri/elimina', 'ApiController@eliminaFiltro')->name('api.officina.filtri.elimina');
    Route::get('filtri/', 'ApiController@filtri')->name('api.officina.filtri');
});

// API route (/api/biblioteca)
Route::group(['prefix' => 'biblioteca', 'namespace' => 'App\Biblioteca\Controllers'], function () {
    Route::post('/autori', 'ApiController@postAutore')->name('api.biblioteca.autori.create');
    Route::post('/editori', 'ApiController@postEditore')->name('api.biblioteca.editori.create');
    Route::get('/autore', 'ApiController@autocompleteAutori')->name('api.biblioteca.autori');
    Route::get('/editore', 'ApiController@autocompleteEditori')->name('api.biblioteca.editori');
    Route::get('/collocazione', 'ApiController@autocompleteCollocazione')->name('api.biblioteca.collocazione');
    Route::get('/titolo', 'ApiController@autocompleteTitolo')->name('api.biblioteca.titolo');
});

//Api route /api/patente
Route::group(['prefix' => 'patente', 'namespace' => 'App\Patente\Controllers'], function () {
    Route::post('/', 'ApiController@create')->name('api.patente.create'); // crea una nuova patente
    Route::get('/persone', 'ApiController@persone')->name('api.patente.persone');
    Route::get('/rilasciata', 'ApiController@rilasciata')->name('api.patente.rilascio');
    Route::get('/persone/senzapatente', 'ApiController@personeSenzaPatente')->name('api.patente.persone.senzapatente');
    Route::get('/categorie', 'ApiController@categorie')->name('api.patente.categorie');
    Route::get('/cqc', 'ApiController@cqc')->name('api.patente.cqc');
    Route::get('/{numero}', 'ApiController@patente')->name('api.patente');
    Route::put('/{numero}', 'ApiController@update')->name('api.patente.modifica'); // modifica una nuova patente
    Route::get('/{numero}/categorie', 'ApiController@patenteCategorie')->name('api.patente.categorie.assegnate');
    // Route::put('/{numero}/categorie', 'ApiController@patenteCategorieAggiungi')->name("api.patente.categorie.aggiungi");
});
