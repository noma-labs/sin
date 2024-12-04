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

//API route: /api/nomadelfia
Route::group(['prefix' => 'nomadelfia', 'namespace' => 'App\Nomadelfia\Api\Controllers'], function () {
    Route::get('/persone/{id}', 'ApiController@persona')->name('api.nomadelfia.persona');
    Route::get('/famiglie', 'ApiController@famiglie')->name('api.nomadeflia.famiglie');
    Route::get('/gruppi', 'ApiController@gruppi')->name('api.nomadeflia.gruppi');
    Route::post('/famiglie/create', 'ApiController@famigliaCreate')->name('api.nomadeflia.famiglie.create');
    Route::get('/posizioni', 'ApiController@posizioniAll')->name('api.nomadeflia.posizioni');
    Route::get('/azienda/edit/{id}', [ApiController::class, 'aziendaEdit'])->name('api.nomadeflia.azienda.edit');
    Route::get('/azienda/mansioni', 'ApiController@mansioni')->name('api.nomadeflia.azienda.mansioni');
    Route::get('/azienda/stati', 'ApiController@stati')->name('api.nomadeflia.azienda.stati');
    Route::get('/aziende/lavoratore/{id}', 'ApiController@aziendeLavoratore')->name('api.nomadeflia.aziende.lavoratori');
    Route::post('/azienda/modifica/lavoratore', 'ApiController@modificaLavoratore')->name('api.nomadeflia.azienda.modifica.lavoratore');
    Route::post('/azienda/sposta/lavoratore', 'ApiController@spostaLavoratore')->name('api.nomadeflia.azienda.sposta.lavoratore');
    Route::get('/azienda/aggiungi/search', [ApiController::class, 'autocompleteLavoratore'])->name('api.nomadelfia.azienda.persone');
    Route::post('/azienda/aggiungi/lavoratore', [ApiController::class, 'aggiungiNuovoLavoratore'])->name('api.nomadelfia.azienda.aggiungi.lavoratore');
    // INCARICHI
    Route::get('/incarichi/edit/{id}', 'ApiController@incarichiEdit')->name('api.nomadeflia.incarichi.edit');
    Route::post('/incarichi/sposta/lavoratore', 'ApiController@incarichiSpostaLavoratore')->name('api.nomadeflia.incarichi.sposta.lavoratore');
    Route::get('/incarichi/lavoratore/{id}', 'ApiController@incarichiLavoratore')->name('api.nomadeflia.incarichi.lavoratori');
    Route::post('/incarichi/aggiungi/lavoratore', 'ApiController@incarichiAggiungiNuovoLavoratore')->name('api.nomadelfia.incarichi.aggiungi.lavoratore');
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
