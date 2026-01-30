<?php

declare(strict_types=1);

use App\Admin\Controllers\LogsActivityController;
use App\Admin\Controllers\PermissionController;
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
use App\Nomadelfia\Famiglia\Controllers\FamilyController;
use App\Nomadelfia\Famiglia\Controllers\FamilyGruppofamiliareController;
use App\Nomadelfia\Famiglia\Controllers\FamilyLeaveController;
use App\Nomadelfia\Famiglia\Controllers\FamilyMemberController;
use App\Nomadelfia\Famiglia\Controllers\MarriageController;
use App\Nomadelfia\Famiglia\Controllers\PersonaFamigliaController;
use App\Nomadelfia\GruppoFamiliare\Controllers\CapogruppoController;
use App\Nomadelfia\GruppoFamiliare\Controllers\GruppofamiliareController;
use App\Nomadelfia\GruppoFamiliare\Controllers\MovePersonGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonGruppoFamiliareController;
use App\Nomadelfia\Incarico\Controllers\IncarichiController;
use App\Nomadelfia\Persona\Controllers\DeathController;
use App\Nomadelfia\Persona\Controllers\FolderNumberController;
use App\Nomadelfia\Persona\Controllers\InternalNameController;
use App\Nomadelfia\Persona\Controllers\JoinCommunityController;
use App\Nomadelfia\Persona\Controllers\LeaveCommunityController;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonaStatoController;
use App\Nomadelfia\Persona\Controllers\PersonController;
use App\Nomadelfia\Persona\Controllers\PersonIdentityController;
use App\Nomadelfia\Persona\Controllers\PersonPositionController;
use App\Nomadelfia\Persona\Controllers\SearchablePersonController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\CaricheController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\JoinLeaveHistoryController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PrintableExcelPopolazioneController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PrintableWordPopolazioneController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\RecentActivitesController;
use App\Officina\Controllers\FiltersController;
use App\Officina\Controllers\OilsController;
use App\Officina\Controllers\PatentiController;
use App\Officina\Controllers\ReservationsController;
use App\Officina\Controllers\SearchableReservationsController;
use App\Officina\Controllers\TiresController;
use App\Officina\Controllers\VehicleDisposalController;
use App\Officina\Controllers\VehiclesController;
use App\Officina\Controllers\VehicleTiresController;
use App\Patente\Controllers\PatenteCategorieController;
use App\Patente\Controllers\PatenteController;
use App\Patente\Controllers\PatenteCQCController;
use App\Patente\Controllers\PatenteElenchiController;
use App\Patente\Controllers\PatenteSearchController;
use App\Photo\Controllers\FaceController;
use App\Photo\Controllers\FavouritesController;
use App\Photo\Controllers\LegendController;
use App\Photo\Controllers\PhotoController;
use App\Photo\Controllers\SlideshowController;
use App\Photo\Controllers\StripesController;
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

Route::view('home', 'home')->name('home');

Route::get('login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('guest');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->middleware('role:super-admin')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('users-new', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::get('users/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('roles-new', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{id}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('logs', [LogsActivityController::class, 'index'])->name('logs.index');
});

Route::prefix('nomadelfia')->middleware('auth')->group(function () {
    Route::get('/', [PopolazioneSummaryController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.index');

    Route::get('people-new', [PersonController::class, 'create'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.person.create');
    Route::post('people', [PersonController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.person.store');
    Route::get('people/{id}', [PersonController::class, 'show'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.person.show');

    Route::get('people/{id}/identity', [PersonIdentityController::class, 'edit'])->middleware('can:popolazione.persona.modifica')->name('nomadelfia.person.identity.edit');
    Route::put('people/{id}/identity', [PersonIdentityController::class, 'update'])->middleware('can:popolazione.persona.modifica')->name('nomadelfia.person.identity.update');

    Route::get('people/{id}/join', [JoinCommunityController::class, 'create'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.join.create');
    Route::post('people/{id}/join', [JoinCommunityController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.join.store');
    Route::put('people/{id}/join/{entrata}', [JoinCommunityController::class, 'update'])->middleware('can:popolazione.persona.modifica')->name('nomadelfia.join.update');
    Route::post('people/{id}/leave', [LeaveCommunityController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.leave.store');
    Route::post('people/{id}/leave/{uscita}', [LeaveCommunityController::class, 'update'])->middleware('can:popolazione.persona.modifica')->name('nomadelfia.leave.update');
    Route::get('people/{id}/join-leave-history', [JoinLeaveHistoryController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.join-leave-history.index');

    Route::post('people/{id}/death', [DeathController::class, 'store'])->middleware('can:popolazione.persona.modifica')->name('nomadelfia.death.store');
    Route::get('people/{id}/folder-number', [FolderNumberController::class, 'create'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.folder-number.create');
    Route::post('people/{id}/folder-number', [FolderNumberController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.folder-number.store');

    Route::post('people/{id}/internal-name', [InternalNameController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.internal-name.store');
    Route::get('people/{id}/internal-name', [InternalNameController::class, 'edit'])->middleware('can:popolazione.persona.modifica')->name('nomadelfia.internal-name.edit');
    Route::put('people/{id}/internal-name', [InternalNameController::class, 'update'])->middleware('can:popolazione.persona.modifica')->name('nomadelfia.internal-name.update');

    Route::view('search', 'nomadelfia.persone.search')->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.people.search');
    Route::get('search/submit', [SearchablePersonController::class, 'show'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.people.search.show');

    Route::get('people/{id}/stato', [PersonaStatoController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.persone.stato');
    Route::post('people/{id}/stato', [PersonaStatoController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.persone.stato.assegna');
    Route::put('people/{id}/stato/{idStato}', [PersonaStatoController::class, 'update'])->name('nomadelfia.persone.stato.modifica');

    Route::get('people/{id}/position', [PersonPositionController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.person.position.index');
    Route::post('people/{id}/position', [PersonPositionController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.person.position.store');
    Route::put('people/{id}/position/{idPos}', [PersonPositionController::class, 'update'])->name('nomadelfia.person.position.update');
    Route::delete('people/{id}/position/{idPos}', [PersonPositionController::class, 'delete'])->name('nomadelfia.person.position.delete');
    Route::post('people/{id}/position/{idPos}/end', [PersonaPosizioneConcludiController::class, 'store'])->name('nomadelfia.person.position.end');

    Route::get('people/{id}/gruppofamiliare', [PersonGruppoFamiliareController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.person.gruppo');
    Route::post('people/{id}/gruppofamiliare', [PersonGruppoFamiliareController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.person.gruppo.store');
    // FIXME: add an id for the "gruppi_persone" to uniquely update, delete, or move apersone. The <id, idGruppo> key does not uniquely identify
    Route::put('people/{id}/gruppofamiliare/{idGruppo}', [PersonGruppoFamiliareController::class, 'update'])->name('nomadelfia.person.gruppo.update');
    Route::delete('people/{id}/gruppofamiliare/{idGruppo}', [PersonGruppoFamiliareController::class, 'delete'])->name('nomadelfia.persone.gruppo.delete');
    Route::post('people/{id}/gruppofamiliare/{idGruppo}/sposta', [MovePersonGruppoFamiliareController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('nomadelfia.persone.gruppo.sposta');

    Route::get('people/{idPersona}/aziende', [PersonaAziendeController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.persone.aziende');
    Route::post('people/{idPersona}/aziende', [PersonaAziendeController::class, 'store'])->name('nomadelfia.persone.aziende.assegna');
    Route::post('people/{idPersona}/aziende/{id}/modifica', [PersonaAziendeController::class, 'update'])->name('nomadelfia.persone.aziende.modifica');
    Route::get('aziende', [AziendeController::class, 'view'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.aziende');
    Route::get('aziende/edit/{id}', [AziendeController::class, 'edit'])->name('nomadelfia.aziende.edit');
    Route::post('aziende/{id}/persona', [AziendeLavoratoreController::class, 'store'])->name('nomadelfia.azienda.lavoratore.assegna');
    Route::put('aziende/{id}/persona/{idPersona}/sposta', [AziendeLavoratoreController::class, 'sposta'])->name('nomadelfia.aziende.persona.sposta');
    Route::put('aziende/{id}/persona/{idPersona}', [AziendeLavoratoreController::class, 'update'])->name('nomadelfia.aziende.persona.update');
    // FIXME: add an id for aziene_persone to uniquely identify them. This delete remove all the aziende peronse association
    Route::delete('aziende/{id}/persona/{idPersona}', [AziendeLavoratoreController::class, 'delete'])->name('nomadelfia.aziende.persona.delete');

    Route::get('incarichi', [IncarichiController::class, 'view'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.incarichi.index');
    Route::get('incarichi/edit/{id}', [IncarichiController::class, 'edit'])->name('nomadelfia.incarichi.edit');
    Route::post('incarichi', [IncarichiController::class, 'insert'])->name('nomadelfia.incarichi.aggiungi');
    Route::delete('incarichi/{id}', [IncarichiController::class, 'delete'])->name('nomadelfia.incarichi.delete');
    Route::post('incarichi/{id}/assegna', [IncarichiController::class, 'assegnaPersona'])->name('nomadelfia.incarichi.assegna');
    Route::delete('incarichi/{id}/persone/{idPersona}', [IncarichiController::class, 'eliminaPersona'])->name('nomadelfia.incarichi.persone.elimina');

    Route::get('gruppifamiliari', [GruppofamiliareController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.gruppifamiliari');
    Route::get('gruppifamiliari/{id}', [GruppofamiliareController::class, 'show'])->name('nomadelfia.gruppifamiliari.show');
    Route::post('gruppifamiliari/{id}/capogruppo', [CapogruppoController::class, 'store'])->name('nomadelfia.capogruppo.store');

    Route::get('families', [FamilyController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('nomadelfia.families');
    Route::get('families/{id}', [FamilyController::class, 'show'])->name('nomadelfia.families.show');
    Route::put('families/{id}', [FamilyController::class, 'update'])->name('nomadelfia.families.update');
    Route::get('people/{id}/families', [PersonaFamigliaController::class, 'index'])->name('nomadelfia.person.families');

    Route::get('marriage', [MarriageController::class, 'create'])->name('nomadelfia.marriage.create');
    Route::post('marriage', [MarriageController::class, 'store'])->name('nomadelfia.marriage.store');

    Route::post('families/{id}/leave', [FamilyLeaveController::class, 'store'])->name('nomadelfia.family.leave');
    Route::post('families/{id}/member', [FamilyMemberController::class, 'store'])->name('nomadelfia.family.member.store');
    Route::put('families/{id}/member', [FamilyMemberController::class, 'update'])->name('nomadelfia.family.member.update');
    Route::post('families/{id}/gruppo/{currentGruppo}', [FamilyGruppofamiliareController::class, 'store'])->name('nomadelfia.family.gruppo.move');

    Route::get('export/word', PrintableWordPopolazioneController::class)->name('nomadelfia.popolazione.export.word');
    Route::get('export/excel', PrintableExcelPopolazioneController::class)->name('nomadelfia.popolazione.export.excel');

    Route::get('popolazione', [PopolazioneNomadelfiaController::class, 'index'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione');
    Route::get('popolazione/positions/maggiorenni', [PopolazioneNomadelfiaController::class, 'maggiorenni'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.maggiorenni');
    Route::get('popolazione/positions/effettivi', [PopolazioneNomadelfiaController::class, 'effettivi'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.posizione.effettivi');
    Route::get('popolazione/positions/postulanti', [PopolazioneNomadelfiaController::class, 'postulanti'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.posizione.postulanti');
    Route::get('popolazione/positions/figlimaggiorenni', [PopolazioneNomadelfiaController::class, 'figliMaggiorenni'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.posizione.figli.maggiorenni');
    Route::get('popolazione/positions/figliminorenni', [PopolazioneNomadelfiaController::class, 'figliMinorenni'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.posizione.figli.minorenni');
    Route::get('popolazione/positions/ospiti', [PopolazioneNomadelfiaController::class, 'ospiti'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.posizione.ospiti');
    Route::get('popolazione/stati/sacerdoti', [PopolazioneNomadelfiaController::class, 'sacerdoti'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.stati.sacerdoti');
    Route::get('popolazione/stati/mamvocazione', [PopolazioneNomadelfiaController::class, 'mammeVocazione'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.stati.mammevocazione');
    Route::get('popolazione/stati/nommamme', [PopolazioneNomadelfiaController::class, 'nomadelfaMamma'])->middleware('can:popolazione.visualizza')->name('nomadelfia.popolazione.stati.nomadelfamamma');

    Route::get('esercizi', [EsSpiritualiController::class, 'index'])->name('nomadelfia.esercizi');
    Route::get('esercizi/stampa', [EsSpiritualiController::class, 'stampa'])->name('nomadelfia.esercizi.stampa');
    Route::get('esercizi/{id}', [EsSpiritualiController::class, 'show'])->name('nomadelfia.esercizi.dettaglio');
    Route::post('esercizi/{id}/assegna', [EsSpiritualiController::class, 'assegn]aPersona'])->name('nomadelfia.esercizi.assegna');
    Route::delete('esercizi/{id}/persona/{idPersona}', [EsSpiritualiController::class, 'elimin]aPersona'])->name('nomadelfia.esercizi.elimina');

    Route::get('cariche', [CaricheController::class, 'index'])->name('nomadelfia.cariche.index');
    Route::get('elezioni', [CaricheController::class, 'elezioni'])->name('nomadelfia.cariche.elezioni');
    Route::get('elezioni/esporta', [CaricheController::class, 'esporta'])->name('nomadelfia.cariche.esporta');

    Route::get('recent-activites', [RecentActivitesController::class, 'index'])->middleware('can:popolazione.visualizza')->name('nomadelfia.activity');
});

Route::prefix('scuola')->middleware('auth')->group(function () {
    Route::get('/', [ScuolaController::class, 'summary'])->name('scuola.summary');
    Route::get('/anni/storico', [ScuolaController::class, 'storico'])->name('scuola.anno.storico');
    Route::post('stampa', [ScuolaController::class, 'print'])->name('scuola.stampa');

    Route::get('/anno/{id}', [AnnoScolasticoController::class, 'show'])->name('scuola.anno.show');
    Route::get('/anno/{id}/new', [AnnoScolasticoController::class, 'showNew'])->name('scuola.anno.show.new');
    Route::post('/anno/{id}/clone', [AnnoScolasticoController::class, 'clone'])->name('scuola.anno.clone');
    Route::put('/anno/{id}/note', AnnoScolasticoNoteController::class)->name('scuola.anno.note.update');
    Route::post('/anno', [AnnoScolasticoController::class, 'store'])->name('scuola.anno.aggiungi');
    Route::post('anno/{id}/classe', [AnnoScolasticoClassiController::class, 'store'])->name('scuola.anno.classe.aggiungi');

    Route::get('classi/{id}', [ClassiController::class, 'show'])->name('scuola.classi.show');
    Route::delete('classi/{id}', [ClassiController::class, 'delete'])->name('scuola.classi.rimuovi');
    Route::get('classi/{id}/elaborato', [ClassiElaboratiController::class, 'create'])->name('scuola.classi.elaborato.create');
    Route::post('classi/{id}/assegna/coordinatore', [ClassiCoordinatoriController::class, 'store'])->name('scuola.classi.coordinatore.assegna');
    Route::post('classi/{id}/assegna/alunno', [ClassiController::class, 'aggiungiAlunno'])->name('scuola.classi.alunno.assegna');
    Route::post('classi/{id}/rimuovi/{alunno_id}', [ClassiController::class, 'rimuoviAlunno'])->name('scuola.classi.alunno.rimuovi');
    Route::post('classi/{id}/rimuovi/{coord_id}/coordinatore', [ClassiCoordinatoriController::class, 'delete'])->name('scuola.classi.coordinatore.rimuovi');
    Route::put('classi/{id}/tipo', [ClassiTipoController::class, 'update'])->name('scuola.classi.tipo.update');
    Route::put('classi/{id}/note', ClassiNoteController::class)->name('scuola.classi.note.update');

    Route::get('elaborati/summary', [ElaboratiController::class, 'index'])->name('scuola.elaborati.index');
    Route::get('elaborati', [ElaboratiController::class, 'create'])->name('scuola.elaborati.create');
    Route::post('elaborati', [ElaboratiController::class, 'store'])->name('scuola.elaborati.store');
    Route::post('elaborati/{id}/upload', [ElaboratiMediaController::class, 'store'])->name('scuola.elaborati.media.store');
    Route::get('elaborati/{id}', [ElaboratiController::class, 'show'])->name('scuola.elaborati.show');
    Route::get('elaborati/{id}/edit', [ElaboratiController::class, 'edit'])->name('scuola.elaborati.edit');
    Route::put('elaborati/{id}', [ElaboratiController::class, 'update'])->name('scuola.elaborati.update');
    Route::get('elaborati/{id}/download', [ElaboratiController::class, 'download'])->name('scuola.elaborati.download');
    Route::get('elaborati/{id}/preview', [ElaboratiController::class, 'preview'])->name('scuola.elaborati.preview');
    Route::get('elaborati/{id}/students', [ElaboratiStudentsController::class, 'create'])->name('scuola.elaborati.students.create');
    Route::post('elaborati/{id}/students', [ElaboratiStudentsController::class, 'store'])->name('scuola.elaborati.students.store');

    Route::get('elaborati/{id}/cover', [CoverImageController::class, 'create'])->name('scuola.elaborati.cover.create');
    Route::post('elaborati/{id}/cover', [CoverImageController::class, 'store'])->name('scuola.elaborati.cover.store');
    Route::get('elaborati/{id}/cover-show', [CoverImageController::class, 'show'])->name('scuola.elaborati.cover.show');

    Route::get('students/{id}', [StudentController::class, 'show'])->name('scuola.student.show');
    Route::get('students/{id}/works', [StudentWorksController::class, 'show'])->name('scuola.student.works.show');
    Route::get('students/{id}/classes', [StudentClassesController::class, 'show'])->name('scuola.student.classes.show');
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

    Route::get('authors', [AuthorsController::class, 'index'])->middleware('can:biblioteca.autore.visualizza')->name('authors.index');
    Route::get('authors/{id}', [AuthorsController::class, 'show'])->middleware('can:biblioteca.autore.visualizza')->name('authors.show');
    Route::get('authors-new', [AuthorsController::class, 'create'])->middleware('can:biblioteca.autore.inserisci')->name('authors.create');
    Route::post('authors', [AuthorsController::class, 'store'])->middleware('can:biblioteca.autore.inserisci')->name('authors.store');
    Route::get('authors/{id}/edit', [AuthorsController::class, 'edit'])->middleware('can:biblioteca.autore.visualizza')->name('authors.edit');
    Route::put('authors/{id}', [AuthorsController::class, 'update'])->middleware('can:biblioteca.autore.visualizza')->name('authors.update');

    Route::get('editors', [EditorsController::class, 'index'])->name('editors.index');
    Route::get('editors/{id}', [EditorsController::class, 'show'])->middleware('can:biblioteca.editore.visualizza')->name('editors.show');
    Route::get('editors-new', [EditorsController::class, 'create'])->middleware('can:biblioteca.editore.inserisci')->name('editors.create');
    Route::post('editors', [EditorsController::class, 'store'])->middleware('can:biblioteca.editore.inserisci')->name('editors.store');
    Route::get('editors/{id}/edit', [EditorsController::class, 'edit'])->middleware('can:biblioteca.editore.visualizza')->name('editors.edit');
    Route::put('editors/{id}', [EditorsController::class, 'update'])->middleware('can:biblioteca.editore.visualizza')->name('editors.update');

    Route::get('audiences', [ClassificazioniController::class, 'index'])->middleware('can:biblioteca.libro.visualizza')->name('audience.index');
    Route::get('audiences-new', [ClassificazioniController::class, 'create'])->middleware('can:biblioteca.libro.visualizza')->name('audience.create');
    Route::post('audiences', [ClassificazioniController::class, 'store'])->middleware('can:biblioteca.libro.visualizza')->name('audience.store');
    Route::put('audiences/{id}', [ClassificazioniController::class, 'update'])->middleware('can:biblioteca.libro.visualizza')->name('audience.update');
    Route::get('audiences/{id}', [ClassificazioniController::class, 'edit'])->middleware('can:biblioteca.libro.visualizza')->name('audience.edit');
    Route::delete('audiences/{id}', [ClassificazioniController::class, 'destroy'])->middleware('can:biblioteca.libro.visualizza')->name('audience.destroy');

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

    Route::get('/maintenance/search', [SearchableMaintenanceController::class, 'show'])->middleware('can:agraria.*')->name('agraria.maintenanace.search.show');
    Route::get('/maintenance', [MaintenanceController::class, 'create'])->middleware('can:agraria.*')->name('agraria.maintenanace.create');
    Route::get('/maintenance/{id}', [MaintenanceController::class, 'show'])->middleware('can:agraria.*')->name('agraria.maintenanace.show');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->middleware('can:agraria.*')->name('agraria.maintenanace.store');
    Route::get('/maintenance/{id}/edit', [MaintenanceController::class, 'edit'])->middleware('can:agraria.*')->name('agraria.maintenanace.edit');
    Route::put('/maintenance/{id}', [MaintenanceController::class, 'update'])->middleware('can:agraria.*')->name('agraria.maintenanace.update');
    Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->middleware('can:agraria.*')->name('agraria.maintenanace.destroy');

    Route::get('/planned-maintenance', [PlannedMaintenanceController::class, 'index'])->middleware('can:agraria.*')->name('agraria.maintenanace.planned.index');
    Route::post('/planned-maintenance', [PlannedMaintenanceController::class, 'store'])->middleware('can:agraria.*')->name('agraria.maintenanace.planned.store');
});

Route::prefix('officina')->middleware('auth')->group(function () {
    Route::get('reservations-new', [ReservationsController::class, 'create'])->middleware('can:meccanica.veicolo.prenota')->name('officina.index');
    Route::post('reservations', [ReservationsController::class, 'store'])->middleware('can:meccanica.prenotazione.inserisci')->name('officina.prenota');
    Route::delete('reservations/{id}', [ReservationsController::class, 'delete'])->middleware('can:meccanica.prenotazione.elimina')->name('officina.prenota.delete');
    Route::get('reservations/{id}', [ReservationsController::class, 'edit'])->middleware('can:meccanica.prenotazione.modifica')->name('officina.prenota.modifica');
    Route::put('reservations/{id}', [ReservationsController::class, 'update'])->middleware('can:meccanica.prenotazione.modifica')->name('officina.prenota.update');

    Route::get('reservations', [SearchableReservationsController::class, 'search'])->middleware('can:meccanica.prenotazione.visualizza')->name('officina.ricerca');

    Route::get('veichels', [VehiclesController::class, 'index'])->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.index');
    Route::get('veichels-new', [VehiclesController::class, 'create'])->middleware('can:meccanica.veicolo.inserisci')->name('veicoli.create');
    Route::post('veichels', [VehiclesController::class, 'store'])->middleware('can:meccanica.veicolo.inserisci')->name('veicoli.store');
    Route::get('veichels/{id}', [VehiclesController::class, 'show'])->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.dettaglio');
    Route::delete('veichels/{id}', [VehiclesController::class, 'destroy'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.demolisci');
    Route::get('veichels/{id}/edit', [VehiclesController::class, 'edit'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.modifica');
    Route::put('veichels/{id}', [VehiclesController::class, 'update'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.update');

    Route::get('disposed', [VehicleDisposalController::class, 'index'])->middleware('can:meccanica.veicolo.visualizza')->name('veicoli.demoliti');
    Route::put('disposed/{id}', [VehicleDisposalController::class, 'update'])->middleware('can:meccanica.veicolo.modifica')->name('veicolo.riabilita');
    Route::delete('disposed/{id}', [VehicleDisposalController::class, 'destroy'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.elimina.definitivamente');

    Route::delete('veichels/{id}/tires/{idGomma}', [VehicleTiresController::class, 'delete'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.tires.delete');
    Route::post('veichels/{id}/tires', [VehicleTiresController::class, 'store'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.tires.store');

    Route::get('filters', [FiltersController::class, 'index'])->middleware('can:meccanica.veicolo.modifica')->name('filtri');
    Route::post('filters', [FiltersController::class, 'store'])->middleware('can:meccanica.veicolo.modifica')->name('filtri.aggiungi');
    Route::delete('filters/{id}', [FiltersController::class, 'delete'])->middleware('can:meccanica.veicolo.modifica')->name('filtri.delete');

    Route::post('oils', [OilsController::class, 'store'])->middleware('can:meccanica.veicolo.modifica')->name('olio.aggiungi');
    Route::post('tires', [TiresController::class, 'store'])->middleware('can:meccanica.veicolo.modifica')->name('gomma.aggiungi');
    Route::get('patents', PatentiController::class)->middleware('can:meccanica.veicolo.visualizza')->name('officina.patenti');
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

Route::prefix('photos')->middleware('auth')->group(function () {
    Route::get('/', [PhotoController::class, 'index'])->middleware('can:photo.view')->name('photos.index');
    Route::get('/stripes', [StripesController::class, 'index'])->middleware('can:photo.view')->name('photos.stripes.index');
    Route::get('/stripes/{stripe}', [StripesController::class, 'show'])->middleware('can:photo.view')->name('photos.stripes.show');
    Route::get('/slideshow', [SlideshowController::class, 'index'])->middleware('can:photo.view')->name('photos.slideshow');

    Route::get('/favourite', [FavouritesController::class, 'index'])->middleware('can:photo.view')->name('photos.favorite.index');
    Route::post('/{id}/favourite', [FavouritesController::class, 'store'])->middleware('can:photo.store')->name('photos.favorite');
    Route::put('/{id}/favourite', [FavouritesController::class, 'destroy'])->name('photos.unfavorite');
    Route::get('/favourite/download', [FavouritesController::class, 'download'])->middleware('can:photo.download')->name('photos.favorite.download');
    Route::get('/favourite/export', [LegendController::class, 'index'])->middleware('can:photo.download')->name('photos.legend');

    Route::get('/faces', [FaceController::class, 'index'])->middleware('can:photo.view')->name('photos.face.index');
    Route::get('/faces/{name}', [FaceController::class, 'show'])->middleware('can:photo.view')->name('photos.face.show');

    Route::put('/{id}', [PhotoController::class, 'update'])->middleware('can:photo.update')->name('photos.update');
    Route::get('/{id}', [PhotoController::class, 'show'])->middleware('can:photo.view')->name('photos.show');
    Route::get('/{id}/download', [PhotoController::class, 'download'])->middleware('can:photo.download')->name('photos.download');
    Route::get('/{id}/preview', [PhotoController::class, 'preview'])->middleware('can:photo.view')->name('photos.preview');

});

Route::get('/debug-sentry', function () {
    throw new Exception('A fake sentry error!');
});
