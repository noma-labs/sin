<?php

declare(strict_types=1);

use App\Admin\Controllers\LogsActivityController;
use App\Admin\Controllers\RisorsaController;
use App\Admin\Controllers\RoleController;
use App\Admin\Controllers\UserController;
use App\Agraria\Controllers\AgrariaController;
use App\Agraria\Controllers\MaintenanceController;
use App\Agraria\Controllers\MezziController;
use App\Agraria\Controllers\PlannedMaintenanceController;
use App\Agraria\Controllers\SearchableMaintenanceController;
use App\Agraria\Controllers\VehicleHourController;
use App\ArchivioDocumenti\Controllers\ArchivioDocumentiController;
use App\Auth\Controllers\LoginController;
use App\Biblioteca\Controllers\AuthorsController;
use App\Biblioteca\Controllers\BooksBorrowController;
use App\Biblioteca\Controllers\BooksCallNumberController;
use App\Biblioteca\Controllers\BooksController;
use App\Biblioteca\Controllers\BooksDeletedController;
use App\Biblioteca\Controllers\ClassificazioniController;
use App\Biblioteca\Controllers\EditorsController;
use App\Biblioteca\Controllers\LabelsController;
use App\Biblioteca\Controllers\LoansController;
use App\Biblioteca\Controllers\SearchableBooksController;
use App\Biblioteca\Controllers\VideoController;
use App\Nomadelfia\Azienda\Controllers\AziendeController;
use App\Nomadelfia\Azienda\Controllers\AziendeLavoratoreController;
use App\Nomadelfia\Azienda\Controllers\PersonaAziendeController;
use App\Nomadelfia\EserciziSpirituali\Controllers\EsSpiritualiController;
use App\Nomadelfia\Famiglia\Controllers\FamiglieController;
use App\Nomadelfia\Famiglia\Controllers\MatrimonioController;
use App\Nomadelfia\Famiglia\Controllers\PersonaFamigliaController;
use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonaGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonaGruppoFamiliareSpostaController;
use App\Nomadelfia\Incarico\Controllers\IncarichiController;
use App\Nomadelfia\Persona\Controllers\PersonIdentityController;
use App\Nomadelfia\Persona\Controllers\PersonaDecessoController;
use App\Nomadelfia\Persona\Controllers\JoinCommunityController;
use App\Nomadelfia\Persona\Controllers\PersonaNominativoController;
use App\Nomadelfia\Persona\Controllers\PersonaNumeroElencoController;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneController;
use App\Nomadelfia\Persona\Controllers\PersonaStatoController;
use App\Nomadelfia\Persona\Controllers\LeaveCommunityController;
use App\Nomadelfia\Persona\Controllers\PersonController;
use App\Nomadelfia\Persona\Controllers\SearchablePersonaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\AggiornamentoAnagrafeController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\CaricheController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PersonaPopolazioneController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PrintableExcelPopolazioneController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PrintablePopolazioneController;
use App\Officina\Controllers\FiltriController;
use App\Officina\Controllers\GommeController;
use App\Officina\Controllers\PatentiController;
use App\Officina\Controllers\PrenotazioniController;
use App\Officina\Controllers\VeicoliController;
use App\Officina\Controllers\VeicoliGommeController;
use App\Patente\Controllers\PatenteCategorieController;
use App\Patente\Controllers\PatenteController;
use App\Patente\Controllers\PatenteCQCController;
use App\Patente\Controllers\PatenteElenchiController;
use App\Patente\Controllers\PatenteSearchController;
use App\Rtn\Video\VideoController as RtnVideoController;
use App\Scuola\Controllers\AnnoScolasticoClassiController;
use App\Scuola\Controllers\AnnoScolasticoController;
use App\Scuola\Controllers\AnnoScolasticoNoteController;
use App\Scuola\Controllers\ClassiController;
use App\Scuola\Controllers\ClassiCoordinatoriController;
use App\Scuola\Controllers\ClassiElaboratiController;
use App\Scuola\Controllers\ClassiNoteController;
use App\Scuola\Controllers\ClassiTipoController;
use App\Scuola\Controllers\CoverImageController;
use App\Scuola\Controllers\ElaboratiController;
use App\Scuola\Controllers\ElaboratiMediaController;
use App\Scuola\Controllers\ElaboratiStudentsController;
use App\Scuola\Controllers\ScuolaController;
use App\Scuola\Controllers\StudentClassesController;
use App\Scuola\Controllers\StudentController;
use App\Scuola\Controllers\StudentWorksController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('/home', 'home')->name('home');

Route::group([], function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('guest');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->middleware('role:super-admin')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('admin');
    Route::put('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('roles', RoleController::class);
    Route::get('risorse', [RisorsaController::class, 'index']);
    Route::get('logs', [LogsActivityController::class, 'index'])->name('admin.logs');
});

Route::prefix('nomadelfia')->middleware('auth')->name('nomadelfia.')->group(function () {
    Route::get('/', [PopolazioneSummaryController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('index');

    Route::get('people', [PersonController::class, 'create'])->name('person.create');
    Route::post('people', [PersonController::class, 'store'])->name('person.store');
    Route::get('people/{id}', [PersonController::class, 'show'])->middleware('can:popolazione.persona.visualizza')->name('person.show');

    Route::get('people/{id}/identity', [PersonIdentityController::class, 'edit'])->name('person.identity.edit');
    Route::put('people/{id}/identity', [PersonIdentityController::class, 'update'])->name('person.identity.update');

    Route::get('people/{id}/join', [JoinCommunityController::class, 'create'])->name('join.create');
    Route::post('people/{id}/join', [JoinCommunityController::class, 'store'])->name('join.store');
    Route::put('people/{id}/join/{entrata}', [JoinCommunityController::class, 'update'])->name('join.update');

    Route::post('people/{id}/leave', [LeaveCommunityController::class, 'store'])->name('leave.store');
    Route::post('people/{id}/leave/{uscita}', [LeaveCommunityController::class, 'update'])->name('leave.update');

    Route::post('persone/{idPersona}/decesso', [PersonaDecessoController::class, 'store'])->name('persone.decesso');
    Route::get('persone/{idPersona}/numelenco', [PersonaNumeroElencoController::class, 'edit'])->name('persone.numelenco.modifica.view');
    Route::put('persone/{idPersona}/numelenco', [PersonaNumeroElencoController::class, 'update'])->name('persone.numelenco.confirm');
    Route::get('persone/{idPersona}/nominativo', [PersonaNominativoController::class, 'edit'])->middleware('can:popolazione.persona.modifica')->name('persone.nominativo.modifica.view');
    Route::put('persone/{idPersona}/nominativo', [PersonaNominativoController::class, 'update'])->middleware('can:popolazione.persona.modifica')->name('persone.nominativo.modifica');
    Route::post('persone/{idPersona}/nominativo', [PersonaNominativoController::class, 'store'])->middleware('can:popolazione.persona.modifica')->name('persone.nominativo.assegna');
    Route::get('persone/ricerca/test', [SearchablePersonaController::class, 'index'])->name('persone.ricerca');
    Route::get('persone/ricerca/submit', [SearchablePersonaController::class, 'show'])->name('persone.ricerca.submit');
    Route::get('persone/{idPersona}/popolazione', [PersonaPopolazioneController::class, 'index'])->name('persone.popolazione');
    Route::get('persone/{idPersona}/stato', [PersonaStatoController::class, 'index'])->name('persone.stato');
    Route::post('persone/{idPersona}/stato', [PersonaStatoController::class, 'store'])->name('persone.stato.assegna');
    Route::put('persone/{idPersona}/stato/{id}', [PersonaStatoController::class, 'update'])->name('persone.stato.modifica');
    Route::get('persone/{idPersona}/posizione', [PersonaPosizioneController::class, 'index'])->name('persone.posizione');
    Route::post('persone/{idPersona}/posizione', [PersonaPosizioneController::class, 'store'])->name('persone.posizione.assegna');
    Route::put('persone/{idPersona}/posizione/{id}', [PersonaPosizioneController::class, 'update'])->name('persone.posizione.modifica');
    Route::delete('persone/{idPersona}/posizione/{id}', [PersonaPosizioneController::class, 'delete'])->name('persone.posizione.elimina');
    Route::post('persone/{idPersona}/posizione/{id}/concludi', [PersonaPosizioneConcludiController::class, 'store'])->name('persone.posizione.concludi');
    Route::get('persone/{idPersona}/gruppofamiliare', [PersonaGruppoFamiliareController::class, 'index'])->name('persone.gruppofamiliare');
    Route::post('persone/{idPersona}/gruppofamiliare', [PersonaGruppoFamiliareController::class, 'store'])->name('persone.gruppo.assegna');
    Route::put('persone/{idPersona}/gruppofamiliare/{id}', [PersonaGruppoFamiliareController::class, 'update'])->name('persone.gruppo.modifica');
    Route::delete('persone/{idPersona}/gruppofamiliare/{id}', [PersonaGruppoFamiliareController::class, 'delete'])->name('persone.gruppo.elimina');
    Route::post('persone/{idPersona}/gruppofamiliare/{id}/sposta', [PersonaGruppoFamiliareSpostaController::class, 'store'])->name('persone.gruppo.sposta');
    Route::get('persone/{idPersona}/aziende', [PersonaAziendeController::class, 'index'])->name('persone.aziende');
    Route::post('persone/{idPersona}/aziende', [PersonaAziendeController::class, 'store'])->name('persone.aziende.assegna');
    Route::post('persone/{idPersona}/aziende/{id}/modifica', [PersonaAziendeController::class, 'update'])->name('persone.aziende.modifica');
    Route::get('persone/{idPersona}/famiglie', [PersonaFamigliaController::class, 'index'])->name('persone.famiglie');

    Route::get('aziende', [AziendeController::class, 'view'])->name('aziende');
    Route::get('aziende/edit/{id}', [AziendeController::class, 'edit'])->name('aziende.edit');
    Route::post('aziende/{id}/persona', [AziendeLavoratoreController::class, 'store'])->name('azienda.lavoratore.assegna');
    Route::put('aziende/{id}/persona/{idPersona}/sposta', [AziendeLavoratoreController::class, 'sposta'])->name('aziende.persona.sposta');
    Route::put('aziende/{id}/persona/{idPersona}', [AziendeLavoratoreController::class, 'update'])->name('aziende.persona.update');
    Route::delete('aziende/{id}/persona/{idPersona}', [AziendeLavoratoreController::class, 'delete'])->name('aziende.persona.delete');

    Route::get('incarichi', [IncarichiController::class, 'view'])->name('incarichi.index');
    Route::get('incarichi/edit/{id}', [IncarichiController::class, 'edit'])->name('incarichi.edit');
    Route::post('incarichi', [IncarichiController::class, 'insert'])->name('incarichi.aggiungi');
    Route::delete('incarichi/{id}', [IncarichiController::class, 'delete'])->name('incarichi.delete');
    Route::post('incarichi/{id}/assegna', [IncarichiController::class, 'assegnaPersona'])->name('incarichi.assegna');
    Route::delete('incarichi/{id}/persone/{idPersona}', [IncarichiController::class, 'eliminaPersona'])->name('incarichi.persone.elimina');

    Route::get('gruppifamiliari', [GruppifamiliariController::class, 'view'])->name('gruppifamiliari');
    Route::get('gruppifamiliari/{id}', [GruppifamiliariController::class, 'edit'])->name('gruppifamiliari.dettaglio');
    // TODO: GruppoFamilireCapogruppo@store
    Route::post('gruppifamiliari/{id}/capogruppo', [GruppifamiliariController::class, 'assegnaCapogruppo'])->name('gruppifamiliari.capogruppo');

    Route::get('famiglie', [FamiglieController::class, 'view'])->name('famiglie');
    Route::post('famiglie/create', [FamiglieController::class, 'createConfirm'])->name('famiglie.create.confirm');
    Route::get('famiglie/{id}', [FamiglieController::class, 'show'])->name('famiglia.dettaglio');
    Route::post('famiglie/{id}/aggiorna/', [FamiglieController::class, 'update'])->name('famiglia.aggiorna');

    Route::get('matrimonio/create', [MatrimonioController::class, 'create'])->name('matrimonio.create');
    Route::post('matrimonio/store', [MatrimonioController::class, 'store'])->name('matrimonio.store');

    // TODO FamigliaUscitaController@store
    Route::post('famiglie/{id}/uscita', [FamiglieController::class, 'uscita'])->name('famiglie.uscita');
    // TODO FamigliaGruppoFamiliareController@store|delete
    Route::post('famiglie/{id}/gruppo/{currentGruppo}/assegna', [FamiglieController::class, 'spostaInGruppoFamiliare'])->name('famiglie.gruppo.sposta');
    Route::delete('famiglie/{id}/gruppo/{idGruppo}', [FamiglieController::class, 'eliminaGruppoFamiliare'])->name('famiglie.gruppo.elimina');
    // TODO FamigliaComponenteController@store|update
    Route::post('famiglie/{id}/componente/assegna', [FamiglieController::class, 'assegnaComponente'])->name('famiglie.componente.assegna');
    Route::post('famiglie/{id}/componente/aggiorna', [FamiglieController::class, 'aggiornaComponente'])->name('famiglie.componente.aggiorna');

    // TODO PrintablePopolazioneController@store
    Route::post('popolazione/stampa', [PopolazioneNomadelfiaController::class, 'print'])->name('popolazione.stampa');
    Route::get('popolazione/excel', [PrintableExcelPopolazioneController::class, 'store'])->name('popolazione.export.excel');
    Route::get('popolazione/stampa/preview', [PopolazioneNomadelfiaController::class, 'preview'])->name('popolazione.anteprima');

    Route::get('popolazione/', [PopolazioneNomadelfiaController::class, 'index'])->name('popolazione');
    Route::get('popolazione/posizione/maggiorenni', [PopolazioneNomadelfiaController::class, 'maggiorenni'])->name('popolazione.maggiorenni');
    Route::get('popolazione/posizione/effettivi', [PopolazioneNomadelfiaController::class, 'effettivi'])->name('popolazione.posizione.effettivi');
    Route::get('popolazione/posizione/postulanti', [PopolazioneNomadelfiaController::class, 'postulanti'])->name('popolazione.posizione.postulanti');
    Route::get('popolazione/posizione/figlimaggiorenni', [PopolazioneNomadelfiaController::class, 'figliMaggiorenni'])->name('popolazione.posizione.figli.maggiorenni');
    Route::get('popolazione/posizione/figliminorenni', [PopolazioneNomadelfiaController::class, 'figliMinorenni'])->name('popolazione.posizione.figli.minorenni');
    Route::get('popolazione/posizione/ospiti', [PopolazioneNomadelfiaController::class, 'ospiti'])->name('popolazione.posizione.ospiti');
    Route::get('popolazione/stati/sacerdoti', [PopolazioneNomadelfiaController::class, 'sacerdoti'])->name('popolazione.stati.sacerdoti');
    Route::get('popolazione/stati/mamvocazione', [PopolazioneNomadelfiaController::class, 'mammeVocazione'])->name('popolazione.stati.mammevocazione');
    Route::get('popolazione/stati/nommamme', [PopolazioneNomadelfiaController::class, 'nomadelfaMamma'])->name('popolazione.stati.nomadelfamamma');

    Route::get('esercizi/', [EsSpiritualiController::class, 'index'])->name('esercizi');
    Route::get('esercizi/stampa', [EsSpiritualiController::class, 'stampa'])->name('esercizi.stampa');
    Route::get('esercizi/{id}', [EsSpiritualiController::class, 'show'])->name('esercizi.dettaglio');
    Route::post('esercizi/{id}/assegna', [EsSpiritualiController::class, 'assegn]aPersona'])->name('esercizi.assegna');
    Route::delete('esercizi/{id}/persona/{idPersona}', [EsSpiritualiController::class, 'elimin]aPersona'])->name('esercizi.elimina');

    Route::get('cariche/', [CaricheController::class, 'index'])->name('cariche.index');
    Route::get('elezioni', [CaricheController::class, 'elezioni'])->name('cariche.elezioni');
    Route::get('elezioni/esporta', [CaricheController::class, 'esporta'])->name('cariche.esporta');

    Route::get('activity/', [AggiornamentoAnagrafeController::class, 'index'])->name('activity');
});

Route::prefix('scuola')->middleware('auth')->name('scuola.')->group(function () {
    Route::get('/', [ScuolaController::class, 'summary'])->name('summary');
    Route::get('/anni/storico', [ScuolaController::class, 'storico'])->name('anno.storico');
    Route::post('stampa', [ScuolaController::class, 'print'])->name('stampa');

    Route::get('/anno/{id}', [AnnoScolasticoController::class, 'show'])->name('anno.show');
    Route::get('/anno/{id}/new', [AnnoScolasticoController::class, 'showNew'])->name('anno.show.new');
    Route::post('/anno/{id}/clone', [AnnoScolasticoController::class, 'clone'])->name('anno.clone');
    Route::put('/anno/{id}/note', AnnoScolasticoNoteController::class)->name('anno.note.update');
    Route::post('/anno', [AnnoScolasticoController::class, 'store'])->name('anno.aggiungi');
    Route::post('anno/{id}/classe', [AnnoScolasticoClassiController::class, 'store'])->name('anno.classe.aggiungi');

    Route::get('classi/{id}', [ClassiController::class, 'show'])->name('classi.show');
    Route::delete('classi/{id}', [ClassiController::class, 'delete'])->name('classi.rimuovi');
    Route::get('classi/{id}/elaborato', [ClassiElaboratiController::class, 'create'])->name('classi.elaborato.create');
    Route::post('classi/{id}/assegna/coordinatore', [ClassiCoordinatoriController::class, 'store'])->name('classi.coordinatore.assegna');
    Route::post('classi/{id}/assegna/alunno', [ClassiController::class, 'aggiungiAlunno'])->name('classi.alunno.assegna');
    Route::post('classi/{id}/rimuovi/{alunno_id}', [ClassiController::class, 'rimuoviAlunno'])->name('classi.alunno.rimuovi');
    Route::post('classi/{id}/rimuovi/{coord_id}/coordinatore', [ClassiCoordinatoriController::class, 'delete'])->name('classi.coordinatore.rimuovi');
    Route::put('classi/{id}/tipo', [ClassiTipoController::class, 'update'])->name('classi.tipo.update');
    Route::put('classi/{id}/note', ClassiNoteController::class)->name('classi.note.update');

    Route::get('elaborati/summary', [ElaboratiController::class, 'index'])->name('elaborati.index');
    Route::get('elaborati', [ElaboratiController::class, 'create'])->name('elaborati.create');
    Route::post('elaborati', [ElaboratiController::class, 'store'])->name('elaborati.store');
    Route::post('elaborati/{id}/upload', [ElaboratiMediaController::class, 'store'])->name('elaborati.media.store');
    Route::get('elaborati/{id}', [ElaboratiController::class, 'show'])->name('elaborati.show');
    Route::get('elaborati/{id}/edit', [ElaboratiController::class, 'edit'])->name('elaborati.edit');
    Route::put('elaborati/{id}', [ElaboratiController::class, 'update'])->name('elaborati.update');
    Route::get('elaborati/{id}/download', [ElaboratiController::class, 'download'])->name('elaborati.download');
    Route::get('elaborati/{id}/preview', [ElaboratiController::class, 'preview'])->name('elaborati.preview');
    Route::get('elaborati/{id}/students', [ElaboratiStudentsController::class, 'create'])->name('elaborati.students.create');
    Route::post('elaborati/{id}/students', [ElaboratiStudentsController::class, 'store'])->name('elaborati.students.store');
    Route::get('elaborati/{id}/cover', [CoverImageController::class, 'create'])->name('elaborati.cover.create');
    Route::post('elaborati/{id}/cover', [CoverImageController::class, 'store'])->name('elaborati.cover.store');

    Route::get('students/{id}', [StudentController::class, 'show'])->name('student.show');
    Route::get('students/{id}/works', [StudentWorksController::class, 'show'])->name('student.works.show');
    Route::get('students/{id}/classes', [StudentClassesController::class, 'show'])->name('student.classes.show');
});

Route::prefix('biblioteca')->middleware('auth')->group(function () {
    Route::view('/', 'biblioteca.index')->withoutMiddleware('auth')->name('biblioteca');
    Route::get('books', [SearchableBooksController::class, 'index'])->withoutMiddleware('auth')->name('books.index');
    Route::get('books-search', [SearchableBooksController::class, 'search'])->withoutMiddleware('auth')->name('books.search');

    Route::get('books-new', [BooksController::class, 'create'])->middleware('can:biblioteca.libro.inserisci')->name('books.create');
    Route::post('books-new', [BooksController::class, 'store'])->middleware('can:biblioteca.libro.inserisci')->name('books.store');
    Route::get('books/{id}/edit', [BooksController::class, 'edit'])->middleware('can:biblioteca.libro.modifica')->name('books.edit');
    Route::put('books/{id}', [BooksController::class, 'update'])->middleware('can:biblioteca.libro.modifica')->name('books.update');
    Route::get('books/{id}', [BooksController::class, 'show'])->middleware('can:biblioteca.libro.prenota')->name('books.show');

    Route::get('books/{id}/call-number', [BooksCallNumberController::class, 'show'])->middleware('can:biblioteca.libro.visualizza')->name('books.call-number');
    Route::put('books/{id}/call-number', [BooksCallNumberController::class, 'update'])->middleware('can:biblioteca.libro.modifica')->name('books.call-number.update');
    Route::get('books/{id}/call-number/{idTarget}', [BooksCallNumberController::class, 'swapShow'])->middleware('can:biblioteca.libro.modifica')->name('books.call-number.swap');
    Route::put('books/{id}/call-number/{idTarget}', [BooksCallNumberController::class, 'swapUpdate'])->middleware('can:biblioteca.libro.modifica')->name('books.call-number.swap.update');

    Route::get('loans', [LoansController::class, 'index'])->middleware('can:biblioteca.libro.prenota')->name('books.loans');
    Route::get('loans/search', [LoansController::class, 'search'])->middleware('can:biblioteca.libro.visualizza')->name('books.loans.search');
    Route::get('loans/{id}', [LoansController::class, 'show'])->middleware('can:biblioteca.libro.visualizza')->name('books.loans.show');
    Route::get('loans/{id}/edit', [LoansController::class, 'edit'])->middleware('can:biblioteca.libro.modifica')->name('books.loans.edit');
    Route::put('loans/{id}/edit', [LoansController::class, 'update'])->middleware('can:biblioteca.libro.modifica')->name('books.loans.update');
    Route::put('loans/{id}/return', [LoansController::class, 'return'])->middleware('can:biblioteca.libro.prenota')->name('books.loans.return');

    Route::get('books/{id}/borrow', [BooksBorrowController::class, 'create'])->middleware('can:biblioteca.libro.prenota')->name('books.borrow');
    Route::post('books/{id}/borrow', [BooksBorrowController::class, 'store'])->middleware('can:biblioteca.libro.prenota')->name('books.borrow.store');

    Route::get('books-trashed', [BooksDeletedController::class, 'index'])->name('books.trashed');
    Route::get('books/{id}/destroy', [BooksDeletedController::class, 'create'])->middleware('can:biblioteca.libro.elimina')->name('books.destory.create');
    Route::delete('books/{id}', [BooksDeletedController::class, 'destory'])->middleware('can:biblioteca.libro.elimina')->name('books.destroy');
    Route::put('books/{id}/restore', [BooksDeletedController::class, 'restore'])->middleware('can:biblioteca.libro.elimina')->name('books.restore');

    Route::get('labels/preview', [LabelsController::class, 'preview'])->withoutMiddleware('auth')->name('books.labels.preview');
    Route::get('labels', [LabelsController::class, 'index'])->middleware('can:biblioteca.etichetta.visualizza')->name('books.labels');
    Route::post('labels', [LabelsController::class, 'storeBatch'])->middleware('can:biblioteca.etichetta.visualizza')->name('books.labels.store-batch');
    Route::post('labels/{idLibro}', [LabelsController::class, 'storeBook'])->middleware('can:biblioteca.etichetta.inserisci')->name('books.labels.store-book');
    Route::delete('labels/remove', [LabelsController::class, 'removeAll'])->middleware('can:biblioteca.etichetta.elimina')->name('books.labels.delete');
    Route::post('labels/remove/{idLibro}', [LabelsController::class, 'removeLibro'])->middleware('can:biblioteca.etichetta.elimina')->name('books.labels.delete-book');
    Route::get('labels/print', [LabelsController::class, 'printToPdf'])->middleware('can:biblioteca.etichetta.visualizza')->name('books.labels.print');

    Route::group([
        'middleware' => [
            'can:biblioteca.autore.inserisci',
            'can:biblioteca.autore.visualizza',
        ],
    ], function () {
        Route::get('autori/search', [AuthorsController::class, 'search'])->name('autori.ricerca');
        Route::resource('autori', AuthorsController::class);
    });

    Route::group([
        'middleware' => [
            'can:biblioteca.autore.visualizza',
            'can:biblioteca.autore.inserisci',
        ],
    ], function () {
        Route::get('editori/search', [EditorsController::class, 'search'])->name('editori.ricerca');
        Route::resource('editori', EditorsController::class);
    });

    Route::group(['middleware' => ['can:biblioteca.libro.visualizza']], function () {
        Route::resource('classificazioni', ClassificazioniController::class);
    });

    Route::get('video', [VideoController::class, 'showSearchVideoForm'])->name('video');
    Route::get('video/search', [VideoController::class, 'searchConfirm'])->name('video.ricerca.submit');
});

Route::prefix('agraria')->middleware('auth')->group(function () {
    Route::get('/', [AgrariaController::class, 'index'])->middleware('can:agraria.*')->name('agraria.index');
    Route::get('/vechicle', [MezziController::class, 'index'])->middleware('can:agraria.*')->name('agraria.vehichles.index');
    Route::get('/vechicle-new', [MezziController::class, 'create'])->middleware('can:agraria.*')->name('agraria.vehicle.create');
    Route::post('/vechicle', [MezziController::class, 'store'])->middleware('can:agraria.*')->name('agraria.vehicle.store');
    Route::get('/vechicle/{id}', [MezziController::class, 'show'])->middleware('can:agraria.*')->name('agraria.vehicle.show');
    Route::get('/vechicle/{id}/edit', [MezziController::class, 'edit'])->middleware('can:agraria.*')->name('agraria.vehicle.edit');
    Route::put('/vechicle/edit/confirm', [MezziController::class, 'update'])->middleware('can:agraria.*')->name('agraria.vehicle.update');
    Route::get('/hour', [VehicleHourController::class, 'create'])->middleware('can:agraria.*')->name('agraria.vehicle.hour.create');
    Route::post('/hour', [VehicleHourController::class, 'store'])->middleware('can:agraria.*')->name('agraria.vehicle.hour.store');
    Route::get('/maintenance', [MaintenanceController::class, 'create'])->middleware('can:agraria.*')->name('agraria.maintenanace.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->middleware('can:agraria.*')->name('agraria.maintenanace.store');
    Route::get('/planned-maintenance', [PlannedMaintenanceController::class, 'index'])->middleware('can:agraria.*')->name('agraria.maintenanace.planned.index');
    Route::post('/planned-maintenance', [PlannedMaintenanceController::class, 'store'])->middleware('can:agraria.*')->name('agraria.maintenanace.planned.store');
    Route::get('/planned-maintenance/search', [SearchableMaintenanceController::class, 'show'])->middleware('can:agraria.*')->name('agraria.maintenanace.search.show');
});

Route::prefix('officina')->middleware('auth')->group(function () {
    Route::get('/', [PrenotazioniController::class, 'prenotazioni'])->middleware('can:meccanica.veicolo.prenota')->name('officina.index');
    Route::post('/', [PrenotazioniController::class, 'prenotazioniSucc'])->middleware('can:meccanica.prenotazione.inserisci')->name('officina.prenota');
    Route::get('delete/{id}/', [PrenotazioniController::class, 'delete'])->middleware('can:meccanica.prenotazione.elimina')->name('officina.prenota.delete');
    Route::get('modifica/{id}/', [PrenotazioniController::class, 'modifica'])->middleware('can:meccanica.prenotazione.modifica')->name('officina.prenota.modifica');
    Route::post('modifica/{id}/', [PrenotazioniController::class, 'update'])->middleware('can:meccanica.prenotazione.modifica')->name('officina.prenota.update');
    Route::get('all/', [PrenotazioniController::class, 'all'])->middleware('can:meccanica.prenotazione.visualizza')->name('officina.all');
    Route::get('prenotazioni', [PrenotazioniController::class, 'searchView'])->middleware('can:meccanica.prenotazione.visualizza')->name('officina.ricerca');
    Route::get('prenotazioni/search', [PrenotazioniController::class, 'search'])->middleware('can:meccanica.prenotazione.visualizza')->name('officina.ricerca.submit');

    Route::get('veicoli', [VeicoliController::class, 'index'])->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.index');
    Route::get('veicoli/demoliti', [VeicoliController::class, 'veicoliDemoliti'])->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.demoliti');
    Route::post('veicoli/riabilita', [VeicoliController::class, 'veicoloRiabilita'])->middleware('can:meccanica.veicolo.modifica')->name('veicolo.riabilita');
    Route::delete('veicoli/elimina-definitivamente', [VeicoliController::class, 'veicoloEliminaDefinitivamente'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.elimina.definitivamente');
    Route::get('veicoli/nuovo', [VeicoliController::class, 'viewCreate'])->middleware('can:meccanica.veicolo.inserisci')->name('veicoli.nuovo');
    Route::post('veicoli/nuovo', [VeicoliController::class, 'create'])->middleware('can:meccanica.veicolo.inserisci')->name('veicoli.create');
    Route::get('veicoli/{id}', [VeicoliController::class, 'show'])->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.dettaglio');
    Route::delete('veicoli/{id}/gomme/{idGomma}', [VeicoliGommeController::class, 'delete'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.gomme.delete');
    Route::post('veicoli/{id}/gomme', [VeicoliGommeController::class, 'store'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.gomme.store');
    Route::get('veicoli/modifica/{id}', [VeicoliController::class, 'edit'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.modifica');
    Route::post('veicoli/modifica/{id}', [VeicoliController::class, 'editConfirm'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.modifica.confirm');
    Route::delete('demolisci/veicolo', [VeicoliController::class, 'demolisci'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.demolisci');

    Route::post('filtro/aggiungi', [VeicoliController::class, 'aggiungiFiltro'])->middleware('can:meccanica.veicolo.modifica')->name('filtri.aggiungi');
    Route::get('filtri', [FiltriController::class, 'index'])->middleware('can:meccanica.veicolo.modifica')->name('filtri');
    Route::post('filtri', [FiltriController::class, 'store'])->middleware('can:meccanica.veicolo.modifica')->name('filtri.aggiungi');
    Route::delete('filtri/{id}', [FiltriController::class, 'delete'])->middleware('can:meccanica.veicolo.modifica')->name('filtri.delete');
    Route::post('olio/aggiungi', [VeicoliController::class, 'aggiungiOlio'])->middleware('can:meccanica.veicolo.modifica')->name('olio.aggiungi');
    Route::post('gomma', [GommeController::class, 'store'])->middleware('can:meccanica.veicolo.modifica')->name('gomma.aggiungi');
    Route::get('/patenti', [PatentiController::class, 'patenti'])->middleware('can:meccanica.veicolo.visualizza')->name('officina.patenti');
});

Route::prefix('patente')->middleware('auth')->group(function () {
    Route::get('/search', [PatenteSearchController::class, 'searchView'])->middleware('can:scuolaguida.patente.visualizza')->name('patente.ricerca');
    Route::get('/search/confirm', [PatenteSearchController::class, 'search'])->name('patente.ricerca.conferma');

    Route::get('/elenchi', [PatenteElenchiController::class, 'index'])->middleware('can:scuolaguida.patente.visualizza')->name('patente.elenchi');
    Route::get('/elenchi/stampa', [PatenteElenchiController::class, 'stampaAutorizzati'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.autorizzati.esporta.pdf');
    Route::get('/elenchi/preview', [PatenteElenchiController::class, 'stampaAutorizzatiPreview'])->withoutMiddleware('auth')->name('patente.elenchi.autorizzati.esporta.preview');
    Route::get('/elenchi/esporta/excel', [PatenteElenchiController::class, 'autorizzatiEsportaExcel'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.autorizzati.esporta.excel');
    Route::get('/elenchi/patenti/pdf', [PatenteElenchiController::class, 'esportaPatentiPdf'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.patenti.esporta.pdf');
    Route::get('/elenchi/patenti/excel', [PatenteElenchiController::class, 'esportaPatentiExcel'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.patenti.esporta.excel');
    Route::get('/elenchi/cqc/excel', [PatenteElenchiController::class, 'esportaCQCExcel'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.cqc.esporta.excel');

    Route::get('/', [PatenteController::class, 'index'])->middleware('can:scuolaguida.patente.visualizza')->name('patente.scadenze');
    Route::get('/create', [PatenteController::class, 'create'])->middleware('can:scuolaguida.patente.inserisci')->name('patente.create');
    Route::post('/', [PatenteController::class, 'store'])->middleware('can:scuolaguida.patente.inserisci')->name('patente.store');
    Route::get('/{numero}', [PatenteController::class, 'show'])->middleware('can:scuolaguida.patente.modifica')->name('patente.visualizza');
    Route::delete('/{numero}', [PatenteController::class, 'delete'])->middleware('can:scuolaguida.patente.elimina')->name('patente.elimina');
    Route::put('/{numero}', [PatenteController::class, 'update'])->middleware('can:scuolaguida.patente.modifica')->name('patente.update');
    Route::put('/{numero}/categorie', [PatenteCategorieController::class, 'update'])->middleware('can:scuolaguida.patente.modifica')->name('patente.categorie.modifica');
    Route::put('/{numero}/cqc', [PatenteCQCController::class, 'update'])->middleware('can:scuolaguida.patente.modifica')->name('patente.cqc.modifica');
});

Route::prefix('archiviodocumenti')->middleware('auth')->group(function () {
    Route::get('/', [ArchivioDocumentiController::class, 'index'])->name('archiviodocumenti');
    Route::get('/libri/ricerca', [ArchivioDocumentiController::class, 'ricerca'])->name('archiviodocumenti.libri.ricerca');

    Route::get('/etichette', [ArchivioDocumentiController::class, 'etichette'])->name('archiviodocumenti.etichette');
    Route::get('/etichette/export', [ArchivioDocumentiController::class, 'esporta'])->name('libri.etichette.esporta');
    Route::delete('/etichette/delete', [ArchivioDocumentiController::class, 'elimina'])->name('archiviodocumenti.etichette.delete');
    Route::post('/etichette/aggiungi', [ArchivioDocumentiController::class, 'aggiungi'])->name('archiviodocumenti.etichette.aggiungi');
    Route::delete('/etichette/delete/{id}', [ArchivioDocumentiController::class, 'eliminaSingolo'])->name('archiviodocumenti.etichette.rimuovi.singolo');
});

Route::prefix('rtn')->middleware('auth')->group(function () {
    Route::get('/', [RtnVideoController::class, 'index'])->name('rtn.video.index');
    Route::post('/video', [RtnVideoController::class, 'store'])->name('rtn.video.store');
    Route::get('/video', [RtnVideoController::class, 'create'])->name('rtn.video.create');
});

Route::get('/debug-sentry', function () {
    throw new Exception('A fake sentry error!');
});
