<?php

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
Route::group(['prefix' => 'officina','namespace' => 'App\Officina\Controllers'],function(){

  Route::get('/veicoli/search', 'ApiController@searchVeicoli')->name("api.officina.veicoli.search");
  Route::get('/veicoli/prenotazioni', 'ApiController@veicoliPrenotazioni')->name("api.officina.veicoli.prenotazioni");

  Route::get('tipologia', 'ApiController@tipologia')->name('api.officina.tipologia');
  Route::get('alimentazione', 'ApiController@alimentazione')->name('api.officina.alimentazione');
  Route::get('impiego', 'ApiController@impiego')->name('api.officina.impiego');
  Route::get('marche', 'ApiController@marche')->name('api.officina.marche');
  Route::get('clienti', 'ApiController@clientiMeccanica')->name('api.officina.clienti');
  Route::get('veicoli', 'ApiController@veicoli')->name('api.officina.veicoli');
  Route::get('meccanici', 'ApiController@meccanici')->name('api.officina.meccanici');
  // ->middleware('ability:veicolo.visualizza')

});


// API route (/api/biblioteca)
Route::group(['prefix' => 'biblioteca','namespace' => 'App\Biblioteca\Controllers'],function(){
  Route::post('/autori', 'ApiController@postAutore')->name("api.biblioteca.autori.create");
  Route::post('/editori', 'ApiController@postEditore')->name("api.biblioteca.editori.create");
  Route::get('/autore', 'ApiController@autocompleteAutori')->name('api.biblioteca.autori');
  Route::get('/editore', 'ApiController@autocompleteEditori')->name('api.biblioteca.editori');
  Route::get('/collocazione', 'ApiController@autocompleteCollocazione')->name('api.biblioteca.collocazione');
  Route::get('/titolo', 'ApiController@autocompleteTitolo')->name("api.biblioteca.titolo");
  Route::get('/cliente', 'ApiController@autocompleteCliente')->name("api.biblioteca.clienti");
});


//API route: /api/nomadelfia
Route::group(['prefix' => 'nomadelfia', 'namespace' => 'App\Nomadelfia\Controllers'], function(){
  Route::get('/famiglie', 'ApiController@famiglieAll')->name("api.nomadeflia.famiglie");
  Route::post('/famiglie/create', 'ApiController@famigliaCreate')->name("api.nomadeflia.famiglie.create");
  Route::get('/posizioni', 'ApiController@posizioniAll')->name("api.nomadeflia.posizioni");
  Route::get('/azienda/edit/{id}', 'ApiController@aziendaEdit')->name("api.nomadeflia.azienda.edit");
  Route::get('/azienda/mansioni', 'ApiController@mansioni')->name("api.nomadeflia.azienda.mansioni");
  Route::get('/azienda/stati', 'ApiController@stati')->name("api.nomadeflia.azienda.stati");
  Route::get('/aziende/lavoratore/{id}', 'ApiController@aziendeLavoratore')->name("api.nomadeflia.aziende.lavoratori");
  Route::post('/azienda/modifica/lavoratore', 'ApiController@modificaLavoratore')->name("api.nomadeflia.azienda.modifica.lavoratore");
  Route::post('/azienda/sposta/lavoratore', 'ApiController@spostaLavoratore')->name("api.nomadeflia.azienda.sposta.lavoratore");
  Route::get('/azienda/aggiungi/search', 'ApiController@autocompleteLavoratore')->name("api.nomadelfia.azienda.persone");
  Route::post('/azienda/aggiungi/lavoratore', 'ApiController@aggiungiNuovoLavoratore')->name("api.nomadelfia.azienda.aggiungi.lavoratore");
});


//Api route /api/patente
Route::group(['prefix' => 'patente', 'namespace' => 'App\Patente\Controllers'], function(){
  Route::get('/{numero}', 'ApiController@patente')->name("api.patente");
  Route::get('/{numero}/categorie', 'ApiController@patenteCategorie')->name("api.patente.categorie");
});