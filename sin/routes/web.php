<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/','welcome');

Route::group(['namespace' => 'App\Auth\Controllers'], function(){
    // login routes
    Route::get('/login', 'LoginController@showLoginForm')->name("login");
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->name("logout");
    // Registration Routes (Not used)
    // Route::get('register', 'RegisterController@showRegistrationForm')->name("register");
    // Route::post('register', 'RegisterController@register');
    // // Password Reset Routes (not used)
    // Route::get('password/reset/{token?}', 'ResetPasswordController@showResetForm')->name("password.reset");
    // Route::post('password/email', 'ResetPasswordController@sendResetLinkEmail')->name('password.email');
    // Route::post('password/reset', 'ResetPasswordController@reset')->name("password.request");
});

//###################################################################################
//##############  ADMIN (Authentication, Authorization, Backups, Logs) ##############
//###################################################################################

Route::group(['prefix' => 'admin','namespace' => 'App\Admin\Controllers'], function(){
  Route::view("/","admin.index")->middleware('role:admin|master')->name("admin");
  // Authentication
  Route::put('/users/{id}/restore', 'UserController@restore')->middleware('role:admin|master')->name("users.restore");
  Route::resource('users', 'UserController')->middleware('role:admin|master');
  Route::resource('roles', 'RoleController')->middleware('role:admin|master');
  Route::resource('risorse', 'RisorsaController')->middleware('role:admin|master');
  // Backup 
  Route::get('backup', 'BackupController@index')->middleware('role:admin|master')->name("admin.backup");
  Route::get('backup/create', 'BackupController@create')->middleware('role:admin|master')->name("admin.backup.create");
  Route::get('backup/download/{file_name}', 'BackupController@download')->middleware('role:admin|master')->name("admin.backup.download");
  Route::get('backup/delete/{file_name}', 'BackupController@delete')->middleware('role:admin|master')->name("admin.backup.delete");
  //Logs activity
  Route::get('logs', 'LogsActivityController@index')->middleware('role:admin|master')->name("admin.logs");
});

// Home view
Route::view('/home', 'home')->name('home');

// #################################################################
// ###################### DB NOMADELFIA ############################
// ################################################################

Route::group(['prefix' => 'nomadelfia','namespace' => 'App\Nomadelfia\Controllers'],function(){
  Route::view('/', 'nomadelfia.index')->name('nomadelfia');
  // PERSONA
  Route::get('persone', 'PersoneController@view')->name("nomadelfia.persone"); //->middleware('permission:cliente-visualizza')
  Route::get('persone/inserimento/', 'PersoneController@insertView')->name("nomadelfia.persone.inserimento");
  // Route::get('persone/inserimento/completo', 'PersoneController@insertCompletoView')->name("nomadelfia.persone.inserimento.anagrafici");

  Route::get('persone/inserimento/anagrafici', 'PersoneController@insertDatiAnagraficiView')->name("nomadelfia.persone.inserimento.anagrafici");
  Route::post('persone/inserimento/anagrafici', 'PersoneController@insertDatiAnagrafici')->name("nomadelfia.persone.inserimento.anagrafici.confirm");

  Route::get('persone/{idPersona}/inserimento/datinomadelfia', 'PersoneController@insertDatiNomadelfiaView')->name("nomadelfia.persone.inserimento.datinomadelfia");
  Route::post('persone/{idPersona}/inserimento/datinomadelfia', 'PersoneController@insertDatiNomadelfia')->name("nomadelfia.persone.inserimento.datinomadelfia.confirm");
  
  Route::get('persone/{idPersona}/inserimento/famiglia', 'PersoneController@insertFamigliaView')->name("nomadelfia.persone.inserimento.famiglia");
  Route::post('persone/{idPersona}/inserimento/famiglia', 'PersoneController@insertFamiglia')->name("nomadelfia.persone.inserimento.famiglia.confirm");
  

  Route::post('persone/inserimento/initial', 'PersoneController@insertInitial')->name("nomadelfia.persone.inserimento.initial");
  Route::post('persone/inserimento', 'PersoneController@insert')->name("nomadelfia.persone.inserimento.confirm");
  Route::get('persone/{idPersona}', 'PersoneController@show')->name("nomadelfia.persone.dettaglio"); //middleware('permission:cliente-visualizza')
  Route::get('persone/{idPersona}/anagrafica/modifica', 'PersoneController@modificaDatiAnagrafici')->name("nomadelfia.persone.anagrafica.modifica");
  Route::post('persone/{idPersona}/anagrafica/modifica', 'PersoneController@modificaDatiAnagraficiConfirm')->name("nomadelfia.persone.anagrafica.modifica");
  Route::get('persone/{idPersona}/nominativo/modifica', 'PersoneController@modificaNominativo')->name("nomadelfia.persone.nominativo.modifica");
  Route::post('persone/{idPersona}/nominativo/modifica', 'PersoneController@modificaNominativoConfirm')->name("nomadelfia.persone.nominativo.modifica");
  Route::post('persone/{idPersona}/nominativo/assegna', 'PersoneController@assegnaNominativoConfirm')->name("nomadelfia.persone.nominativo.assegna");
  Route::post('persone/{idPersona}/gruppo/modifica', 'PersoneController@modificaGruppoFamiliare')->name("nomadelfia.persone.gruppo.modifica");

  Route::post('persone/{idPersona}/status', 'PersoneController@modficaStatus')->name("nomadelfia.persone.status.modifica");
  
  Route::post('persone/{idPersona}/stato/assegna', 'PersoneController@assegnaStato')->name("nomadelfia.persone.stato.assegna");
  Route::get('persone/{idPersona}/stato', 'PersoneController@stato')->name("nomadelfia.persone.stato");
  Route::post('persone/{idPersona}/stato/{id}/modifica', 'PersoneController@modificaStato')->name("nomadelfia.persone.stato.modifica");

  Route::get('persone/{idPersona}/posizione', 'PersoneController@posizione')->name("nomadelfia.persone.posizione");
  Route::post('persone/{idPersona}/posizione/assegna', 'PersoneController@assegnaPosizione')->name("nomadelfia.persone.posizione.assegna");
  Route::post('persone/{idPersona}/posizione/{id}/modifica', 'PersoneController@modificaPosizione')->name("nomadelfia.persone.posizione.modifica");
  
  Route::get('persone/{idPersona}/categoria', 'PersoneController@categoria')->name("nomadelfia.persone.categoria");
  Route::post('persone/{idPersona}/categoria/{id}/modifica', 'PersoneController@modificaCategoria')->name("nomadelfia.persone.categoria.modifica");
  Route::post('persone/{idPersona}/categoria/assegna', 'PersoneController@assegnaCategoria')->name("nomadelfia.persone.categoria.assegna");

  Route::get('persone/{idPersona}/gruppofamiliare', 'PersoneController@gruppofamiliare')->name("nomadelfia.persone.gruppofamiliare");
  Route::post('persone/{idPersona}/gruppofamiliare/assegna', 'PersoneController@assegnaGruppofamiliare')->name("nomadelfia.persone.gruppo.assegna");
  Route::post('persone/{idPersona}/gruppofamiliare/{id}/modifica', 'PersoneController@modificaGruppofamiliare')->name("nomadelfia.persone.gruppo.modifica");

  Route::get('persone/{idPersona}/aziende', 'PersoneController@aziende')->name("nomadelfia.persone.aziende");
  Route::post('persone/{idPersona}/aziende/assegna', 'PersoneController@assegnaAzienda')->name("nomadelfia.persone.aziende.assegna");
  Route::post('persone/{idPersona}/aziende/{id}/modifica', 'PersoneController@modificaAzienda')->name("nomadelfia.persone.aziende.modifica");





  //AZIENDE
  Route::get('aziende', 'AziendeController@view')->name("nomadelfia.aziende"); //->middleware('permission:cliente-visualizza')
  Route::get('aziende/edit/{id}', 'AziendeController@edit')->name("nomadelfia.aziende.edit");
  
  //GRUPPI FAMILIARI
  Route::get('gruppifamiliari', 'GruppifamiliariController@view')->name("nomadelfia.gruppifamiliari"); //->middleware('permission:cliente-visualizza')
  Route::get('gruppifamiliari/{id}', 'GruppifamiliariController@edit')->name("nomadelfia.gruppifamiliari.dettaglio"); //->middleware('permission:cliente-visualizza')

  // FAMIGLIE
  Route::get('famiglie', 'FamiglieController@view')->name("nomadelfia.famiglie"); //->middleware('permission:cliente-visualizza')
  Route::get('famiglie/create', 'FamiglieController@create')->name("nomadelfia.famiglie.create"); //->middleware('permission:cliente-visualizza')
  Route::post('famiglie/create', 'FamiglieController@createConfirm')->name("nomadelfia.famiglie.create.confirm"); //->middleware('permission:cliente-visualizza')
  Route::get('famiglie/{id}', 'FamiglieController@show')->name("nomadelfia.famiglia.dettaglio"); //->middleware('permission:cliente-visualizza')
  Route::post('famiglie/{id}/gruppo/assegna', 'FamiglieController@assegnaGruppoFamiliare')->name("nomadelfia.famiglie.gruppo.assegna");
  Route::delete('famiglie/{id}/gruppo/{idGruppo}', 'FamiglieController@eliminaGruppoFamiliare')->name("nomadelfia.famiglie.gruppo.elimina");

  Route::post('famiglie/{id}/componente/assegna', 'FamiglieController@assegnaComponente')->name("nomadelfia.famiglie.componente.assegna");
  Route::post('famiglie/{id}/componente/aggiorna', 'FamiglieController@aggiornaComponente')->name("nomadelfia.famiglie.componente.aggiorna");

  //stampa elenchi
  Route::post('popolazione/stampa', 'PopolazioneNomadelfiaController@print')->name("nomadelfia.popolazione.stampa");
  Route::get('popolazione/stampa/preview', 'PopolazioneNomadelfiaController@preview')->name("nomadelfia.popolazione.anteprima");

});

#################################################################
######################  BIBLIOTECA ##############################
#################################################################
Route::group(['prefix' => 'biblioteca','namespace' => 'App\Biblioteca\Controllers'], function(){
  // Route: /biblioteca/
  Route::view('/', 'biblioteca.index')->name('biblioteca'); 
  // LIBRI: ricerca
  Route::get('libri', 'LibriController@showSearchLibriForm')->name("libri.ricerca");
  Route::get('libri/ricerca', 'LibriController@searchConfirm')->name("libri.ricerca.submit");
  // LIBRI: media
  Route::get('libri/{idLibro}/media', 'LibriMediaController@view')->name('libri.media');
  Route::post('libri/{idLibro}/media', 'LibriMediaController@store')->name('libri.media.store');
  Route::delete('libri/{idLibro}/media/{mediaId}', 'LibriMediaController@destroy')->middleware('ability:libro.elimina')->name('libri.media.destroy');
  Route::delete('libri/{idLibro}/media',  'LibriMediaController@destroyAll')->middleware('ability:libro.elimina')->name('libri.media.destroy_all');
  // LIBRI: cambio collocazione
  Route::get('libri/{idLibro}/collocazione', 'LibriController@showEditCollocazioneForm')->middleware('ability:libro.visualizza')->name("libro.collocazione");
  Route::post('libri/{idLibro}/collocazione/update', 'LibriController@updateCollocazione')->middleware('ability:libro.modifica')->name("libro.collocazione.update");
  Route::post('libri/{idLibro}/confirm', 'LibriController@confirmCollocazione')->middleware('ability:libro.modifica')->name("libro.collocazione.update.confirm");
  // LIBRI: inserimento
  Route::get('libri/inserimento', 'LibriController@showInsertLibroForm')->middleware('ability:libro.inserisci')->name('libri.inserisci');
  Route::post('libri/inserimento','LibriController@insertConfirm')->middleware('ability:libro.inserisci')->name('libri.inserisci.Confirm');
  // PRESTITI
  Route::get("libri/prestiti", "LibriPrestitiController@view")->middleware('ability:libro.prenota')->name("libri.prestiti");
  Route::get("libri/prestiti/ricerca", "LibriPrestitiController@search")->middleware('ability:libro.visualizza')->name('libri.prestiti.ricerca');
  Route::get('libri/prestiti/{idPrestito}', 'LibriPrestitiController@show')->middleware('ability:libro.visualizza')->name('libri.prestito');
  Route::get('libri/prestiti/{idPrestito}/modifica', 'LibriPrestitiController@edit')->middleware('ability:libro.modifica')->name('libri.prestito.modifica'); //->middleware('can:edit,App\Libro')->
  Route::post('libri/prestiti/{idPrestito}/modifica', 'LibriPrestitiController@editConfirm')->middleware('ability:libro.modifica');//->name('libri.prestito.modificaConfirm');
  Route::post('libri/prestiti/{idPrestito}/concludi', 'LibriPrestitiController@conclude')->middleware('ability:libro.prenota')->name('libri.prestito.concludi');
  // LIBRI: dettaglio, modifica, elimina, prenota
  Route::get('libri/eliminati','LibriController@showDeleted')->name("libri.eliminati");
  Route::get('libri/{idLibro}',  'LibriController@show')->middleware('ability:libro.prenota')->name('libro.dettaglio');
  Route::get('libri/{idLibro}/modifica',  'LibriController@edit')->middleware('ability:libro.modifica')->name("libro.modifica");
  Route::post('libri/{idLibro}/modifica','LibriController@editConfirm')->middleware('ability:libro.modifica');
  Route::get('libri/{idLibro}/elimina',  'LibriController@delete')->middleware('ability:libro.elimina')->name("libro.elimina");
  Route::post('libri/{idLibro}/elimina','LibriController@deleteConfirm')->middleware('ability:libro.elimina');
  Route::get('libri/{idLibro}/prenota',  'LibriController@book')->middleware('ability:libro.prenota')->name('libri.prenota');
  Route::post('libri/{idLibro}/prenota',  'LibriController@bookConfirm')->middleware('ability:libro.prenota');
  Route::post('libri/{idLibro}/ripristina',  'LibriController@restore')->middleware('ability:libro.elimina')->name('libri.ripristina');
  Route::get('libri/{idLibro}/etichetta',  'LibriController@stampaEtichetta')->middleware('ability:libro.esporta')->name('libri.stampaetichetta');

  // ETICHETTE, aggiungi, rimuovi, preview, stampa
  Route::get('etichette', 'EtichetteController@view')->middleware('ability:etichetta.visualizza')->name("libri.etichette");
  Route::post('etichette', 'EtichetteController@etichetteFromToCollocazione')->middleware('ability:etichetta.visualizza')->name("libri.etichette.aggiungi");
  Route::post('etichette/add/{idLibro}','EtichetteController@addLibro')->middleware('ability:etichetta.inserisci')->name('libri.etichette.aggiungi.libro');
  Route::post('etichette/remove','EtichetteController@removeAll')->middleware('ability:etichetta.elimina')->name('libri.etichette.rimuovi');
  Route::post('etichette/remove/{idLibro}','EtichetteController@removeLibro')->middleware('ability:etichetta.elimina')->name('libri.etichette.rimuovi.libro');
  Route::get('etichette/preview', 'EtichetteController@preview')->middleware('ability:etichetta.visualizza')->name("libri.etichette.preview");
  Route::get('etichette/print', 'EtichetteController@printToPdf')->middleware('ability:etichetta.visualizza')->name("libri.etichette.stampa");
  Route::get('etichette/excel', 'EtichetteController@downloadExcel')->middleware('ability:etichetta.esporta')->name('libri.etichette.excel');
  //AUTORI biblioteca
  Route::group(['middleware' => ['ability:autore.inserisci','ability:autore.visualizza','ability:autore.modifica','ability:autore.elimina']], function () {
    Route::get('autori/search', 'AutoriController@search')->name('autori.ricerca');
    Route::resource('autori', 'AutoriController');
  });
  //EDITORI biblioteca
  Route::group(['middleware' => ['ability:autore.visualizza','ability:autore.inserisci','ability:autore.inserisci', 'ability:autore.elimina']], function () {
    Route::get('editori/search', 'EditoriController@search')->name('editori.ricerca');
    Route::resource('editori', 'EditoriController');
  });
  // CLASSIFICAZIONE: visualizza, modifica, elimina, cerca
  Route::group(['middleware' => ['ability:libro.visualizza']], function () {
    Route::resource('classificazioni', 'ClassificazioniController');
  });
  //  VIDEO biblioteca
  Route::get('video', 'VideoController@showSearchVideoForm')->name("video");
  Route::get('video/search', 'VideoController@searchConfirm')->name("video.ricerca.submit");

});

//##################################################################
//######################## OFFICINA ################################
//##################################################################
Route::group(['prefix' => 'officina','namespace' => 'App\Officina\Controllers'], function(){
  // PRENOTAZIONI add, delete, update, search
  // officina/
  Route::post("/", 'PrenotazioniController@prenotazioniSucc')->middleware('ability:veicolo.prenota')->name('officina.prenota');
 
  Route::get("delete/{id}/", 'PrenotazioniController@delete')->middleware('ability:veicolo.elimina')->name('officina.prenota.delete');
  Route::get("modifica/{id}/", 'PrenotazioniController@modifica')->middleware('ability:veicolo.modifica')->name('officina.prenota.modifica');;
  Route::post("modifica/{id}/", 'PrenotazioniController@update')->middleware('ability:veicolo.modifica')->name('officina.prenota.update');
  Route::get("all/", 'PrenotazioniController@all')->middleware('ability:veicolo.visualizza')->name('officina.all');
  Route::get('prenotazioni', 'PrenotazioniController@searchView')->middleware('ability:veicolo.visualizza')->name('officina.ricerca');
  Route::get('prenotazioni/search', 'PrenotazioniController@search')->middleware('ability:veicolo.visualizza')->name('officina.ricerca.submit');
  // VEICOLI add, update
  Route::get('veicoli', 'VeicoliController@index')->middleware('ability:veicolo.visualizza')->name('veicoli.index');
  Route::get('veicoli/nuovo', 'VeicoliController@viewCreate')->middleware('ability:veicolo.inserisci')->name('veicoli.nuovo');
  Route::post('veicoli/nuovo', 'VeicoliController@create')->middleware('ability:veicolo.inserisci')->name('veicoli.create');
  Route::get('veicoli/{id}','VeicoliController@show')->middleware('ability:veicolo.visualizza')->name('veicoli.dettaglio');
  Route::get('veicoli/modifica/{id}','VeicoliController@edit')->middleware('ability:veicolo.modifica')->name('veicoli.modifica');
  Route::post('veicoli/modifica/{id}','VeicoliController@editConfirm')->middleware('ability:veicolo.modifica')->name('veicoli.modifica.confirm');

  //Patenti
  Route::get("/patenti", 'PatentiController@patenti')->middleware('ability:veicolo.visualizza')->name('officina.patenti');

  Route::get("/{giorno?}", 'PrenotazioniController@prenotazioni')->middleware('ability:veicolo.prenota')->name('officina.index');
});

//#################################################################
//######################   RTN  ###################################
//#################################################################

Route::group(['prefix' => 'rtn','namespace' => 'App\Rtn\Controllers'], function(){
    Route::get('index', 'RtnController@index')->name('rtn.index');
    Route::get('film/search', 'FilmController@search')->name('film.search');
});

//#################################################################
//######################   PATENTE  ###################################
//#################################################################

Route::group(['prefix' => 'patente','namespace' => 'App\Patente\Controllers'], function(){
  Route::get("/", 'PatenteController@scadenze')->middleware('ability:patente.visualizza')->name('patente.scadenze');
  Route::get("/ricerca", 'PatenteController@patente')->middleware('ability:patente.visualizza')->name('patente.ricerca');
  Route::get("/elenchi", 'PatenteController@elenchi')->name('patente.elenchi');
  Route::get("/elenchi/stampa", 'PatenteController@stampaAutorizzati')->name('patente.elenchi.autorizzati.esporta.pdf');
  Route::get("/elenchi/esporta/excel", 'PatenteController@autorizzatiEsportaExcel')->name('patente.elenchi.autorizzati.esporta.excel');
  Route::get("/search", 'PatenteController@ricerca')->name('patente.ricerca.conferma');
  Route::get('modifica/{id}','PatenteController@modifica')->middleware('ability:patente.modifica')->name('patente.modifica');
  Route::get('elimina/{id}','PatenteController@elimina')->middleware('ability:patente.elimina')->name('patente.elimina');
  Route::post('modifica/{id}', 'PatenteController@confermaModifica')->middleware('ability:patente.modifica')->name('patente.modifica.conferma');
  Route::get('inserimento','PatenteController@inserimento')->middleware('ability:patente.inserisci')->name('patente.inserimento');
  Route::post('inserimento', 'PatenteController@confermaInserimento')->middleware('ability:patente.inserisci')->name('patente.inserimento.conferma');
});


//#################################################################
//######################   ARCHIVIO DOCUMENTI ######################
//#################################################################

Route::group(['prefix' => 'archiviodocumenti','namespace' => 'App\ArchivioDocumenti\Controllers'], function(){
  Route::get("/", 'ArchivioDocumentiController@index')->name('archiviodocumenti');
  Route::get("/libri/ricerca", 'ArchivioDocumentiController@ricerca')->name('archiviodocumenti.libri.ricerca');
  Route::get("/etichette", 'ArchivioDocumentiController@etichette')->name('archiviodocumenti.etichette');
  Route::get("/etichette/export", 'ArchivioDocumentiController@esporta')->name('libri.etichette.esporta');
 
  Route::delete("/etichette/delete", 'ArchivioDocumentiController@elimina')->name('archiviodocumenti.etichette.rimuovi');
  Route::post("/etichette/aggiungi", 'ArchivioDocumentiController@aggiungi')->name('archiviodocumenti.etichette.aggiungi');
   
  Route::delete("/etichette/delete/{id}", 'ArchivioDocumentiController@eliminaSingolo')->name('archiviodocumenti.etichette.rimuovi.singolo');

});

