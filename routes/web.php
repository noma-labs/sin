<?php

use App\Biblioteca\Controllers\LibriController;
use App\Nomadelfia\Azienda\Controllers\AziendeController;
use App\Nomadelfia\EserciziSpirituali\Controllers\EsSpiritualiController;
use App\Nomadelfia\Famiglia\Controllers\FamiglieController;
use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use App\Nomadelfia\Incarico\Controllers\IncarichiController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\CaricheController;
use App\Nomadelfia\Persona\Controllers\PersoneController;
use App\Officina\Controllers\PatentiController;
use App\Officina\Controllers\PrenotazioniController;
use App\Patente\Controllers\PatenteController;
use App\Scuola\Controllers\ClassiController;
use App\Scuola\Controllers\ElaboratiController;
use App\Scuola\Controllers\ScuolaController;
use App\Biblioteca\Controllers\LibriPrestitiController;


Route::get('/debug-sentry', function () {
    throw new Exception('My Second Sentry error!');
});

Route::view('/', 'welcome');

Route::group(['namespace' => 'App\Auth\Controllers'], function () {
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

Route::group(['prefix' => 'admin', 'namespace' => 'App\Admin\Controllers'], function () {
    Route::view("/", "admin.index")->name("admin");
    // Authentication
    Route::put('/users/{id}/restore', 'UserController@restore')->name("users.restore");
    Route::resource('users', 'UserController');
    Route::resource('roles', 'RoleController');
    Route::resource('risorse', 'RisorsaController');
    // Backup
    Route::get('backup', 'BackupController@index')->name("admin.backup");
    Route::get('backup/create',
        'BackupController@create')->name("admin.backup.create");
    Route::get('backup/download/{file_name}',
        'BackupController@download')->name("admin.backup.download");
    Route::get('backup/delete/{file_name}',
        'BackupController@delete')->name("admin.backup.delete");
    //Logs activity
    Route::get('logs', 'LogsActivityController@index')->name("admin.logs");
});

// Home view
Route::view('/home', 'home')->name('home');

// #################################################################
// ###################### DB NOMADELFIA ############################
// ################################################################

Route::group(['prefix' => 'nomadelfia', 'namespace' => 'App\Nomadelfia\Controllers'], function () {
    Route::get('/', [PopolazioneNomadelfiaController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia');

    // PERSONA
    Route::get('persone', [PersoneController::class, 'index'])->name('nomadelfia.persone');

    Route::get('persone/inserimento/initial', [PersoneController::class, 'insertInitialView'])->middleware('can:popolazione.persona.inserisci')->name("nomadelfia.persone.inserimento");
    Route::post('persone/inserimento/initial', [PersoneController::class, 'insertInitial'])->name("nomadelfia.persone.inserimento.initial");

    Route::get('persone/inserimento/anagrafici', [PersoneController::class, 'insertDatiAnagraficiView'])->name("nomadelfia.persone.inserimento.anagrafici");
    Route::post('persone/inserimento/anagrafici', [PersoneController::class, 'insertDatiAnagrafici'])->name("nomadelfia.persone.inserimento.anagrafici.confirm");

    // view per selezionare la tipologia di entrata in nomadelfia (dalla nascita oppure no)
    Route::get('persone/{idPersona}/entrata/scelta', [PersoneController::class, 'insertPersonaInternaView'])->name("nomadelfia.persone.inserimento.entrata.scelta.view");
    Route::post('persone/{idPersona}/entrata/scelta', [PersoneController::class, 'insertPersonaInterna'])->name("nomadelfia.persone.inserimento.entrata.scelta");

    Route::post('persone/{idPersona}/decesso', [PersoneController::class, 'decesso'])->name("nomadelfia.persone.decesso");
    Route::post('persone/{idPersona}/uscita', [PersoneController::class, 'uscita'])->name("nomadelfia.persone.uscita");

    Route::get('persone/ricerca/test',
        [PersoneController::class, 'search'])->name("nomadelfia.persone.ricerca"); //->middleware('permission:cliente-visualizza')
    Route::get('persone/ricerca/submit',
        [PersoneController::class, 'searchPersonaSubmit'])->name("nomadelfia.persone.ricerca.submit");

    Route::get('persone/{idPersona}', [PersoneController::class, 'show'])->name("nomadelfia.persone.dettaglio")->middleware('can:popolazione.persona.visualizza');
    Route::delete('persone/{idPersona}', [PersoneController::class, 'rimuovi'])->name("nomadelfia.persone.rimuovi"); //middleware('permission:cliente-visualizza')

    Route::get('persone/{idPersona}/anagrafica/modifica',
        [PersoneController::class, 'modificaDatiAnagrafici'])->name("nomadelfia.persone.anagrafica.modifica.view");
    Route::post('persone/{idPersona}/anagrafica/modifica/confirm',
        [PersoneController::class, 'modificaDatiAnagraficiConfirm'])->name("nomadelfia.persone.anagrafica.modifica.confirm");
    Route::get('persone/{idPersona}/numelenco/assegna',
        [PersoneController::class, 'assegnaNumeroElenco'])->name('nomadelfia.persone.numelenco.modifica.view');
    Route::post('persone/{idPersona}/anagrafica/modifica',
        [PersoneController::class, 'assegnaNumeroElencoConfirm'])->name('nomadelfia.persone.numelenco.confirm');
    Route::get('persone/{idPersona}/nominativo/modifica',
        [PersoneController::class, 'modificaNominativo'])->name("nomadelfia.persone.nominativo.modifica.view");
    Route::post('persone/{idPersona}/nominativo/modifica',
        [PersoneController::class, 'modificaNominativoConfirm'])->name("nomadelfia.persone.nominativo.modifica");
    Route::post('persone/{idPersona}/nominativo/assegna',
        [PersoneController::class, 'assegnaNominativoConfirm'])->name("nomadelfia.persone.nominativo.assegna");

    Route::post('persone/{idPersona}/status',
        [PersoneController::class, 'modficaStatus'])->name("nomadelfia.persone.status.modifica");

    Route::post('persone/{idPersona}/stato/assegna',
        [PersoneController::class, 'assegnaStato'])->name("nomadelfia.persone.stato.assegna");
    Route::get('persone/{idPersona}/stato', [PersoneController::class, 'stato'])->name("nomadelfia.persone.stato");
    Route::post('persone/{idPersona}/stato/{id}/modifica',
        [PersoneController::class, 'modificaStato'])->name("nomadelfia.persone.stato.modifica");

    Route::get('persone/{idPersona}/posizione', [PersoneController::class, 'posizione'])->name("nomadelfia.persone.posizione");
    Route::post('persone/{idPersona}/posizione/assegna',
        [PersoneController::class, 'assegnaPosizione'])->name("nomadelfia.persone.posizione.assegna");
    Route::post('persone/{idPersona}/posizione/{id}/modifica',
        [PersoneController::class, 'modificaDataInizioPosizione'])->name("nomadelfia.persone.posizione.modifica");
    Route::delete('persone/{idPersona}/posizione/{id}',
        [PersoneController::class, 'eliminaPosizione'])->name("nomadelfia.persone.posizione.elimina");
    Route::put('persone/{idPersona}/posizione/{id}/concludi',
        [PersoneController::class, 'concludiPosizione'])->name("nomadelfia.persone.posizione.concludi");

    // TODO: fare la modifica della data di entrata in nomadelfia anche lato frontrns
    Route::post('persone/{idPersona}/entrata/modifica',
        [PersoneController::class, 'updateDataEntrataNomadelfia'])->name("nomadelfia.persone.dataentrata.modifica");

    Route::get('persone/{idPersona}/gruppofamiliare',
        [PersoneController::class, 'gruppofamiliare'])->name("nomadelfia.persone.gruppofamiliare");
    Route::post('persone/{idPersona}/gruppofamiliare/assegna',
        [PersoneController::class, 'assegnaGruppofamiliare'])->name("nomadelfia.persone.gruppo.assegna");

    Route::post('persone/{idPersona}/gruppofamiliare/{id}/modifica',
        [PersoneController::class, 'modificaGruppofamiliare'])->name("nomadelfia.persone.gruppo.modifica");

    Route::delete('persone/{idPersona}/gruppofamiliare/{id}',
        [PersoneController::class, 'eliminaGruppofamiliare'])->name("nomadelfia.persone.gruppo.elimina");
    Route::post('persone/{idPersona}/gruppofamiliare/{id}/concludi',
        [PersoneController::class, 'concludiGruppofamiliare'])->name("nomadelfia.persone.gruppo.concludi");
    Route::post('persone/{idPersona}/gruppofamiliare/{id}/sposta',
        [PersoneController::class, 'spostaNuovoGruppofamiliare'])->name("nomadelfia.persone.gruppo.sposta");

    Route::get('persone/{idPersona}/aziende', [PersoneController::class, 'aziende'])->name("nomadelfia.persone.aziende");
    Route::post('persone/{idPersona}/aziende/assegna',
        [PersoneController::class, 'assegnaAzienda'])->name("nomadelfia.persone.aziende.assegna");
    Route::post('persone/{idPersona}/aziende/{id}/modifica',
        [PersoneController::class, 'modificaAzienda'])->name("nomadelfia.persone.aziende.modifica");

    Route::post('incarichi', [IncarichiController::class, 'insert'])->name("nomadelfia.incarichi.aggiungi");
    Route::delete('incarichi/{id}', [IncarichiController::class, 'delete'])->name("nomadelfia.incarichi.delete");
    Route::post('incarichi/{id}/assegna', [IncarichiController::class, 'assegnaPersona'])->name("nomadelfia.incarichi.assegna");
    Route::delete('incarichi/{id}/persone/{idPersona}', [IncarichiController::class, 'eliminaPersona'])->name("nomadelfia.incarichi.persone.elimina");

    Route::post('persone/{idPersona}/incarichi/assegna', [PersoneController::class, 'assegnaIncarico'])->name("nomadelfia.persone.incarichi.assegna");
    Route::post('persone/{idPersona}/incarichi/{id}/modifica', [PersoneController::class, 'modificaIncarico'])->name("nomadelfia.persone.incarichi.modifica");


    Route::get('persone/{idPersona}/famiglie', [PersoneController::class, 'famiglie'])->name("nomadelfia.persone.famiglie");
    Route::post('persona/{idPersona}/famiglie/create',
        [PersoneController::class, 'createAndAssignFamiglia'])->name("nomadelfia.personae.famiglie.create"); //->middleware('permission:cliente-visualizza')
    Route::post('persona/{idPersona}/famiglie/sposta',
        [PersoneController::class, 'spostaInNuovaFamiglia'])->name("nomadelfia.personae.famiglie.sposta"); //->middleware('permission:cliente-visualizza')


    //AZIENDE
    Route::get('aziende', [AziendeController::class, 'view'])->name("nomadelfia.aziende"); //->middleware('permission:cliente-visualizza')
    Route::get('aziende/edit/{id}', [AziendeController::class, 'edit'])->name("nomadelfia.aziende.edit");

    // INcarichi
    Route::get('incarichi',
        [IncarichiController::class, 'view'])->name("nomadelfia.incarichi.index"); //->middleware('permission:cliente-visualizza')
    Route::get('incarichi/edit/{id}', [IncarichiController::class, 'edit'])->name("nomadelfia.incarichi.edit");


    //GRUPPI FAMILIARI
    Route::get('gruppifamiliari',
        [GruppifamiliariController::class, 'view'])->name("nomadelfia.gruppifamiliari"); //->middleware('permission:cliente-visualizza')
    Route::get('gruppifamiliari/{id}',
        [GruppifamiliariController::class, 'edit'])->name("nomadelfia.gruppifamiliari.dettaglio"); //->middleware('permission:cliente-visualizza')
    Route::post('gruppifamiliari/{id}/capogruppo',
        [GruppifamiliariController::class, 'assegnaCapogruppo'])->name("nomadelfia.gruppifamiliari.capogruppo"); //->middleware('permission:cliente-visualizza')

    // FAMIGLIE
    Route::get('famiglie', [FamiglieController::class, 'view'])->name("nomadelfia.famiglie"); //->middleware('permission:cliente-visualizza')
    Route::get('famiglie/create',
        [FamiglieController::class, 'create'])->name("nomadelfia.famiglie.create"); //->middleware('permission:cliente-visualizza')
    Route::post('famiglie/create',
        [FamiglieController::class, 'createConfirm'])->name("nomadelfia.famiglie.create.confirm"); //->middleware('permission:cliente-visualizza')
    Route::post('famiglie/{id}/uscita',
        [FamiglieController::class, 'uscita'])->name("nomadelfia.famiglie.uscita"); //->middleware('permission:cliente-visualizza')
    Route::get('famiglie/{id}', [FamiglieController::class, 'show'])->name("nomadelfia.famiglia.dettaglio"); //->middleware('permission:cliente-visualizza')
    Route::post('famiglie/{id}/gruppo/{currentGruppo}/assegna',
        [FamiglieController::class, 'spostaInGruppoFamiliare'])->name("nomadelfia.famiglie.gruppo.sposta");
    Route::delete('famiglie/{id}/gruppo/{idGruppo}',
        [FamiglieController::class, 'eliminaGruppoFamiliare'])->name("nomadelfia.famiglie.gruppo.elimina");

    Route::post('famiglie/{id}/aggiorna/', [FamiglieController::class, 'update'])->name("nomadelfia.famiglia.aggiorna");

    Route::post('famiglie/{id}/componente/assegna',
        [FamiglieController::class, 'assegnaComponente'])->name("nomadelfia.famiglie.componente.assegna");
    Route::post('famiglie/{id}/componente/aggiorna',
        [FamiglieController::class, 'aggiornaComponente'])->name("nomadelfia.famiglie.componente.aggiorna");

    //stampa elenchi
    Route::post('popolazione/stampa', [PopolazioneNomadelfiaController::class, 'print'])->name("nomadelfia.popolazione.stampa");
    Route::get('popolazione/stampa/preview',
        [PopolazioneNomadelfiaController::class, 'preview'])->name("nomadelfia.popolazione.anteprima");

    // POPOLAZIONE
    Route::get('popolazione/', [PopolazioneNomadelfiaController::class, 'show'])->name("nomadelfia.popolazione");
    Route::get('popolazione/posizione/maggiorenni', [PopolazioneNomadelfiaController::class, 'maggiorenni'])->name("nomadelfia.popolazione.maggiorenni");
    Route::get('popolazione/posizione/effettivi',
        [PopolazioneNomadelfiaController::class, 'effettivi'])->name("nomadelfia.popolazione.posizione.effettivi");
    Route::get('popolazione/posizione/postulanti',
        [PopolazioneNomadelfiaController::class, 'postulanti'])->name("nomadelfia.popolazione.posizione.postulanti");
    Route::get('popolazione/posizione/figlimaggiorenni',
        [PopolazioneNomadelfiaController::class, 'figliMaggiorenni'])->name("nomadelfia.popolazione.posizione.figli.maggiorenni");
    Route::get('popolazione/posizione/figliminorenni',
        [PopolazioneNomadelfiaController::class, 'figliMinorenni'])->name("nomadelfia.popolazione.posizione.figli.minorenni");
    Route::get('popolazione/posizione/ospiti',
        [PopolazioneNomadelfiaController::class, 'ospiti'])->name("nomadelfia.popolazione.posizione.ospiti");

    Route::get('popolazione/stati/sacerdoti',
        [PopolazioneNomadelfiaController::class, 'sacerdoti'])->name("nomadelfia.popolazione.stati.sacerdoti");
    Route::get('popolazione/stati/mamvocazione',
        [PopolazioneNomadelfiaController::class, 'mammeVocazione'])->name("nomadelfia.popolazione.stati.mammevocazione");
    Route::get('popolazione/stati/nommamme',
        [PopolazioneNomadelfiaController::class, 'nomadelfaMamma'])->name("nomadelfia.popolazione.stati.nomadelfamamma");

    // ESERCIZI SPIRITUALI
    Route::get('esercizi/', [EsSpiritualiController::class, 'index'])->name("nomadelfia.esercizi");
    Route::get('esercizi/stampa', [EsSpiritualiController::class, 'stampa'])->name("nomadelfia.esercizi.stampa");
    Route::get('esercizi/{id}', [EsSpiritualiController::class, 'show'])->name("nomadelfia.esercizi.dettaglio");
    Route::post('esercizi/{id}/assegna', [EsSpiritualiController::class, 'assegn]aPersona'])->name("nomadelfia.esercizi.assegna");
    Route::delete('esercizi/{id}/persona/{idPersona}',
        [EsSpiritualiController::class, 'elimin]aPersona'])->name("nomadelfia.esercizi.elimina");


    //Route::post('persona/{idPersona}/assegna', EsSpiritualiController::class, 'assegnaPersona')->name("nomadelfia.esercizi.persona.assegna");
    // CARICHE COSTITUZIONALI
    Route::get('cariche/', [CaricheController::class, 'index'])->name("nomadelfia.cariche.index");
    Route::get('elezioni', [CaricheController::class, 'elezioni'])->name("nomadelfia.cariche.elezioni");
    Route::get('elezioni/esporta', [CaricheController::class, 'esporta'])->name("nomadelfia.cariche.esporta");


});

// #################################################################
// ###################### DB SCUOLA ############################
// ################################################################

//Route::mediaLibrary();

Route::group(['prefix' => 'scuola', 'namespace' => 'App\Scuola\Controllers'], function () {
    Route::get('/', [ScuolaController::class, 'summary'])->name('scuola.summary');
    Route::get('/anni/storico', [ScuolaController::class, 'storico'])->name('scuola.anno.storico');
    Route::get('/anno/{id}', [ScuolaController::class, 'index'])->name('scuola.anno.show');
    Route::post('/anno/{id}/clone', [ScuolaController::class, 'cloneAnnoScolastico'])->name('scuola.anno.clone');
    Route::post('/anno', [ScuolaController::class, 'aggiungiAnnoScolastico'])->name('scuola.anno.aggiungi');
    Route::post('/anno/{id}/students', [ScuolaController::class, 'importStudentsFromOtherAnnoScolastico'])->name('scuola.anno.import');
    Route::post('anno/{id}', [ScuolaController::class, 'aggiungiClasse'])->name('scuola.anno.classe.aggiungi');
    Route::post('stampa', [ScuolaController::class, 'print'])->name('scuola.stampa');
    Route::get('/anno/{anno_id}/classi', [ClassiController::class, 'index'])->name('scuola.classi');
    Route::get('classi/{id}', [ClassiController::class, 'show'])->name('scuola.classi.show');
    Route::delete('classi/{id}', [ClassiController::class, 'delete'])->name('scuola.classi.rimuovi');
    Route::post('classi/{id}/assegna/coordinatore', 'ClassiController@aggiungiCoordinatore')->name('scuola.classi.coordinatore.assegna');
    Route::post('classi/{id}/assegna/alunno', [ClassiController::class, 'aggiungiAlunno'])->name('scuola.classi.alunno.assegna');
    Route::post('classi/{id}/rimuovi/{alunno_id}', 'ClassiController@rimuoviAlunno')->name('scuola.classi.alunno.rimuovi');
    Route::post('classi/{id}/rimuovi/{coord_id}/coordinatore', [ClassiController::class, 'rimuoviCoordinatore'])->name('scuola.classi.coordinatore.rimuovi');

    // elaborati
    Route::get('elaborati', [ElaboratiController::class, 'index'])->name('scuola.elaborati');
    Route::get('elaborati/insert', [ElaboratiController::class, 'insert'])->name('scuola.elaborati.insert.view');
    Route::post('elaborati/insert', [ElaboratiController::class, 'insertConfirm'])->name('scuola.elaborati.insert');
});

#################################################################
######################  BIBLIOTECA ##############################
#################################################################
Route::group(['prefix' => 'biblioteca', 'namespace' => 'App\Biblioteca\Controllers'], function () {
    // Route: /biblioteca/
    Route::view('/', 'biblioteca.index')->name('biblioteca');
    // LIBRI: ricerca
    Route::get('libri', [LibriController::class, 'showSearchLibriForm'])->name("libri.ricerca");
    Route::get('libri/ricerca', [LibriController::class, 'searchConfirm'])->name("libri.ricerca.submit");
    // LIBRI: media
    Route::get('libri/{idLibro}/media', 'LibriMediaController@view')->name('libri.media');
    Route::post('libri/{idLibro}/media', 'LibriMediaController@store')->name('libri.media.store');
    Route::delete('libri/{idLibro}/media/{mediaId}',
        'LibriMediaController@destroy')->middleware('can:biblioteca.libro.elimina')->name('libri.media.destroy');
    Route::delete('libri/{idLibro}/media',
        'LibriMediaController@destroyAll')->middleware('can:biblioteca.libro.elimina')->name('libri.media.destroy_all');
    // LIBRI: cambio collocazione
    Route::get('libri/{idLibro}/collocazione', [LibriController::class, 'showEditCollocazioneForm'])
        ->middleware('can:biblioteca.libro.visualizza')->name("libro.collocazione");
    Route::post('libri/{idLibro}/collocazione/update', [LibriController::class, 'updateCollocazione'])
        ->middleware('can:biblioteca.libro.modifica')->name("libro.collocazione.update");
    Route::post('libri/{idLibro}/confirm', [LibriController::class, 'confirmCollocazione'])
        ->middleware('can:biblioteca.libro.modifica')->name("libro.collocazione.update.confirm");
    // LIBRI: inserimento
    Route::get('libri/inserimento', [LibriController::class, 'showInsertLibroForm'])
        ->middleware('can:biblioteca.libro.inserisci')->name('libri.inserisci');
    Route::post('libri/inserimento', [LibriController::class, 'insertConfirm'])
        ->middleware('can:biblioteca.libro.inserisci')->name('libri.inserisci.Confirm');
    // PRESTITI
    Route::get("libri/prestiti", [LibriPrestitiController::class, 'view'])->middleware('can:biblioteca.libro.prenota')->name("libri.prestiti");
    Route::get("libri/prestiti/ricerca",
        "LibriPrestitiController@search")->middleware('can:biblioteca.libro.visualizza')->name('libri.prestiti.ricerca');
    Route::get('libri/prestiti/{idPrestito}',
        'LibriPrestitiController@show')->middleware('can:biblioteca.libro.visualizza')->name('libri.prestito');
    Route::get('libri/prestiti/{idPrestito}/modifica',
        'LibriPrestitiController@edit')->middleware('can:biblioteca.libro.modifica')->name('libri.prestito.modifica'); //->middleware('can:edit,App\Libro')->
    Route::post('libri/prestiti/{idPrestito}/modifica',
        'LibriPrestitiController@editConfirm')->middleware('can:biblioteca.libro.modifica');//->name('libri.prestito.modificaConfirm');
    Route::post('libri/prestiti/{idPrestito}/concludi',
        'LibriPrestitiController@conclude')->middleware('can:biblioteca.libro.prenota')->name('libri.prestito.concludi');
    // LIBRI: dettaglio, modifica, elimina, prenota
    Route::get('libri/eliminati', 'LibriController@showDeleted')->name("libri.eliminati");
    Route::get('libri/{idLibro}', [LibriController::class, 'show'])->middleware('can:biblioteca.libro.prenota')->name('libro.dettaglio');
    Route::get('libri/{idLibro}/modifica',
        'LibriController@edit')->middleware('can:biblioteca.libro.modifica')->name("libro.modifica");
    Route::post('libri/{idLibro}/modifica', 'LibriController@editConfirm')->middleware('can:biblioteca.libro.modifica');
    Route::get('libri/{idLibro}/elimina',
        'LibriController@delete')->middleware('can:biblioteca.libro.elimina')->name("libro.elimina");
    Route::post('libri/{idLibro}/elimina', 'LibriController@deleteConfirm')->middleware('can:biblioteca.libro.elimina');
    Route::get('libri/{idLibro}/prenota',
        'LibriController@book')->middleware('can:biblioteca.libro.prenota')->name('libri.prenota');
    Route::post('libri/{idLibro}/prenota', 'LibriController@bookConfirm')->middleware('can:biblioteca.libro.prenota');
    Route::post('libri/{idLibro}/ripristina',
        'LibriController@restore')->middleware('can:biblioteca.libro.elimina')->name('libri.ripristina');
    Route::get('libri/{idLibro}/etichetta',
        'LibriController@stampaEtichetta')->middleware('can:biblioteca.libro.esporta')->name('libri.stampaetichetta');

    // ETICHETTE, aggiungi, rimuovi, preview, stampa
    Route::get('etichette',
        'EtichetteController@view')->middleware('can:biblioteca.etichetta.visualizza')->name("libri.etichette");
    Route::post('etichette',
        'EtichetteController@etichetteFromToCollocazione')->middleware('can:biblioteca.etichetta.visualizza')->name("libri.etichette.aggiungi");
    Route::post('etichette/add/{idLibro}',
        'EtichetteController@addLibro')->middleware('can:biblioteca.etichetta.inserisci')->name('libri.etichette.aggiungi.libro');
    Route::post('etichette/remove',
        'EtichetteController@removeAll')->middleware('can:biblioteca.etichetta.elimina')->name('libri.etichette.rimuovi');
    Route::post('etichette/remove/{idLibro}',
        'EtichetteController@removeLibro')->middleware('can:biblioteca.etichetta.elimina')->name('libri.etichette.rimuovi.libro');
    Route::get('etichette/preview',
        'EtichetteController@preview')->middleware('can:biblioteca.etichetta.visualizza')->name("libri.etichette.preview");
    Route::get('etichette/print',
        'EtichetteController@printToPdf')->middleware('can:biblioteca.etichetta.visualizza')->name("libri.etichette.stampa");
    Route::get('etichette/excel',
        'EtichetteController@downloadExcel')->middleware('can:biblioteca.etichetta.esporta')->name('libri.etichette.excel');
    //AUTORI biblioteca
    Route::group([
        'middleware' => [
            'can:biblioteca.autore.inserisci',
            'can:biblioteca.autore.visualizza'
        ]
    ], function () {
        Route::get('autori/search', 'AutoriController@search')->name('autori.ricerca');
        Route::resource('autori', 'AutoriController');
    });
    //EDITORI biblioteca
    Route::group([
        'middleware' => [
            'can:biblioteca.autore.visualizza',
            'can:biblioteca.autore.inserisci',
        ]
    ], function () {
        Route::get('editori/search', 'EditoriController@search')->name('editori.ricerca');
        Route::resource('editori', 'EditoriController');
    });
    // CLASSIFICAZIONE: visualizza, modifica, elimina, cerca
    Route::group(['middleware' => ['can:biblioteca.libro.visualizza']], function () {
        Route::resource('classificazioni', 'ClassificazioniController');
    });
    //  VIDEO biblioteca
    Route::get('video', 'VideoController@showSearchVideoForm')->name("video");
    Route::get('video/search', 'VideoController@searchConfirm')->name("video.ricerca.submit");
});

//##################################################################
//######################## OFFICINA ################################
//##################################################################
Route::group(['prefix' => 'officina', 'namespace' => 'App\Officina\Controllers'], function () {
    // PRENOTAZIONI add, delete, update, search
    Route::post("/", [PrenotazioniController::class, 'prenotazioniSucc'])->middleware('can:meccanica.prenotazione.inserisci')->name('officina.prenota');

    Route::get("delete/{id}/",
        'PrenotazioniController@delete')->middleware('can:meccanica.prenotazione.elimina')->name('officina.prenota.delete');
    Route::get("modifica/{id}/",
        'PrenotazioniController@modifica')->middleware('can:meccanica.prenotazione.modifica')->name('officina.prenota.modifica');;
    Route::post("modifica/{id}/",
        'PrenotazioniController@update')->middleware('can:meccanica.prenotazione.modifica')->name('officina.prenota.update');
    Route::get("all/", 'PrenotazioniController@all')->middleware('can:meccanica.prenotazione.visualizza')->name('officina.all');
    Route::get('prenotazioni',
        'PrenotazioniController@searchView')->middleware('can:meccanica.prenotazione.visualizza')->name('officina.ricerca');
    Route::get('prenotazioni/search',
        'PrenotazioniController@search')->middleware('can:meccanica.prenotazione.visualizza')->name('officina.ricerca.submit');
    // VEICOLI add, update
    Route::get('veicoli', 'VeicoliController@index')->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.index');
    Route::get('veicoli/demoliti', 'VeicoliController@veicoliDemoliti')->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.demoliti');
    Route::post('veicoli/riabilita', 'VeicoliController@veicoloRiabilita')->middleware('can:meccanica.veicolo.modifica')->name('veicolo.riabilita');
    Route::delete('veicoli/elimina-definitivamente', 'VeicoliController@veicoloEliminaDefinitivamente')->middleware('can:meccanica.veicolo.modifica')->name('veicoli.elimina.definitivamente');
    Route::get('veicoli/nuovo',
        'VeicoliController@viewCreate')->middleware('can:meccanica.veicolo.inserisci')->name('veicoli.nuovo');
    Route::post('veicoli/nuovo',
        'VeicoliController@create')->middleware('can:meccanica.veicolo.inserisci')->name('veicoli.create');
    Route::get('veicoli/{id}',
        'VeicoliController@show')->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.dettaglio');
    Route::get('veicoli/modifica/{id}',
        'VeicoliController@edit')->middleware('can:meccanica.veicolo.modifica')->name('veicoli.modifica');
    Route::post('veicoli/modifica/{id}',
        'VeicoliController@editConfirm')->middleware('can:meccanica.veicolo.modifica')->name('veicoli.modifica.confirm');
    Route::delete('demolisci/veicolo',
        'VeicoliController@demolisci')->middleware('can:meccanica.veicolo.modifica')->name('veicoli.demolisci');
    // Filtri
    Route::post('filtro/aggiungi',
        'VeicoliController@aggiungiFiltro')->middleware('can:meccanica.veicolo.modifica')->name('filtri.aggiungi');
    Route::view('filtri', 'officina.gestione.filtri')->middleware('can:meccanica.veicolo.modifica')->name('filtri');
    // Olio motore
    Route::post('olio/aggiungi',
        'VeicoliController@aggiungiOlio')->middleware('can:meccanica.veicolo.modifica')->name('olio.aggiungi');
    //Patenti
    Route::get("/patenti", [PatentiController::class, 'patenti'])->middleware('can:meccanica.veicolo.visualizza')->name('officina.patenti');

    // PRENOTAZIONI
    Route::get("/", [PrenotazioniController::class, 'prenotazioni'])->middleware('can:meccanica.veicolo.prenota')->name('officina.index');
});

//#################################################################
//######################   RTN  ###################################
//#################################################################

Route::group(['prefix' => 'rtn', 'namespace' => 'App\Rtn\Controllers'], function () {
    Route::get('index', 'RtnController@index')->name('rtn.index');
    Route::get('film/search', 'FilmController@search')->name('film.search');
});

//#################################################################
//######################   PATENTE  ###################################
//#################################################################

Route::group(['prefix' => 'patente', 'namespace' => 'App\Patente\Controllers'], function () {
    Route::get("/", 'PatenteController@scadenze')->middleware('can:scuolaguida.patente.visualizza')->name('patente.scadenze');
    Route::get("/ricerca",
        'PatenteController@patente')->middleware('can:scuolaguida.patente.visualizza')->name('patente.ricerca');
    Route::get("/elenchi",
        'PatenteController@elenchi')->middleware('can:scuolaguida.patente.visualizza')->name('patente.elenchi');
    // esposta elenchi
    Route::get("/elenchi/stampa",
        'PatenteController@stampaAutorizzati')->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.autorizzati.esporta.pdf');
    Route::get("/elenchi/preview", [PatenteController::class, 'stampaAutorizzatiPreview'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.autorizzati.esporta.preview');
    Route::get("/elenchi/esporta/excel",
        'PatenteController@autorizzatiEsportaExcel')->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.autorizzati.esporta.excel');
    Route::get("/elenchi/patenti/pdf",
        'PatenteController@esportaPatentiPdf')->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.patenti.esporta.pdf');
    Route::get("/elenchi/patenti/excel",
        'PatenteController@esportaPatentiExcel')->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.patenti.esporta.excel');
    Route::get("/elenchi/cqc/excel",
        'PatenteController@esportaCQCExcel')->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.cqc.esporta.excel');

    Route::get("/search", 'PatenteController@ricerca')->name('patente.ricerca.conferma');
    Route::get('modifica/{id}',
        'PatenteController@modifica')->middleware('can:scuolaguida.patente.modifica')->name('patente.modifica');
    Route::get('elimina/{id}',
        'PatenteController@elimina')->middleware('can:scuolaguida.patente.elimina')->name('patente.elimina');
    Route::post('modifica/{id}',
        'PatenteController@confermaModifica')->middleware('can:scuolaguida.patente.modifica')->name('patente.modifica.conferma');
    Route::get('inserimento',
        'PatenteController@inserimento')->middleware('can:scuolaguida.patente.inserisci')->name('patente.inserimento');
    Route::post('inserimento',
        'PatenteController@confermaInserimento')->middleware('can:scuolaguida.patente.inserisci')->name('patente.inserimento.conferma');
});


//#################################################################
//######################   ARCHIVIO DOCUMENTI ######################
//#################################################################

Route::group(['prefix' => 'archiviodocumenti', 'namespace' => 'App\ArchivioDocumenti\Controllers'], function () {
    Route::get("/", 'ArchivioDocumentiController@index')->name('archiviodocumenti');
    Route::get("/libri/ricerca", 'ArchivioDocumentiController@ricerca')->name('archiviodocumenti.libri.ricerca');
    Route::get("/etichette", 'ArchivioDocumentiController@etichette')->name('archiviodocumenti.etichette');
    Route::get("/etichette/export", 'ArchivioDocumentiController@esporta')->name('libri.etichette.esporta');

    Route::delete("/etichette/delete",
        'ArchivioDocumentiController@elimina')->name('archiviodocumenti.etichette.rimuovi');
    Route::post("/etichette/aggiungi",
        'ArchivioDocumentiController@aggiungi')->name('archiviodocumenti.etichette.aggiungi');

    Route::delete("/etichette/delete/{id}",
        'ArchivioDocumentiController@eliminaSingolo')->name('archiviodocumenti.etichette.rimuovi.singolo');
});

//#################################################################
//######################   STAZIONE METEO  ###################################
//#################################################################

Route::view('/meteo', 'stazionemeteo.index')->name('stazionemeteo');