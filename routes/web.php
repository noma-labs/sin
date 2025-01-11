<?php

declare(strict_types=1);

use App\Admin\Controllers\LogsActivityController;
use App\Admin\Controllers\RisorsaController;
use App\Admin\Controllers\RoleController;
use App\Admin\Controllers\UserController;
use App\ArchivioDocumenti\Controllers\ArchivioDocumentiController;
use App\Auth\Controllers\LoginController;
use App\Biblioteca\Controllers\AutoriController;
use App\Biblioteca\Controllers\ClassificazioniController;
use App\Biblioteca\Controllers\EditoriController;
use App\Biblioteca\Controllers\EtichetteController;
use App\Biblioteca\Controllers\LibriController;
use App\Biblioteca\Controllers\LibriPrestitiController;
use App\Biblioteca\Controllers\VideoController;
use App\Nomadelfia\Azienda\Controllers\AziendeController;
use App\Nomadelfia\Azienda\Controllers\PersonaAziendeController;
use App\Nomadelfia\EserciziSpirituali\Controllers\EsSpiritualiController;
use App\Nomadelfia\Famiglia\Controllers\FamiglieController;
use App\Nomadelfia\Famiglia\Controllers\MatrimonioController;
use App\Nomadelfia\Famiglia\Controllers\PersonaFamigliaController;
use App\Nomadelfia\GruppoFamiliare\Controllers\GruppifamiliariController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonaGruppoFamiliareController;
use App\Nomadelfia\GruppoFamiliare\Controllers\PersonaGruppoFamiliareSpostaController;
use App\Nomadelfia\Incarico\Controllers\IncarichiController;
use App\Nomadelfia\Persona\Controllers\PersonaAnagraficaController;
use App\Nomadelfia\Persona\Controllers\PersonaDecessoController;
use App\Nomadelfia\Persona\Controllers\PersonaEntrataController;
use App\Nomadelfia\Persona\Controllers\PersonaNominativoController;
use App\Nomadelfia\Persona\Controllers\PersonaNumeroElencoController;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneConcludiController;
use App\Nomadelfia\Persona\Controllers\PersonaPosizioneController;
use App\Nomadelfia\Persona\Controllers\PersonaStatoController;
use App\Nomadelfia\Persona\Controllers\PersonaUscitaController;
use App\Nomadelfia\Persona\Controllers\PersoneController;
use App\Nomadelfia\Persona\Controllers\SearchablePersonaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\AggiornamentoAnagrafeController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\CaricheController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PersonaPopolazioneController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneNomadelfiaController;
use App\Nomadelfia\PopolazioneNomadelfia\Controllers\PopolazioneSummaryController;
use App\Officina\Controllers\PatentiController;
use App\Officina\Controllers\PrenotazioniController;
use App\Officina\Controllers\VeicoliController;
use App\Patente\Controllers\PatenteController;
use App\Rtn\Video\VideoController as RtnVideoController;
use App\Scuola\Controllers\AnnoScolasticoClassiController;
use App\Scuola\Controllers\AnnoScolasticoController;
use App\Scuola\Controllers\AnnoScolasticoNoteController;
use App\Scuola\Controllers\ClassiController;
use App\Scuola\Controllers\ClassiCoordinatoriController;
use App\Scuola\Controllers\ClassiElaboratiController;
use App\Scuola\Controllers\ClassiNoteController;
use App\Scuola\Controllers\ClassiTipoController;
use App\Scuola\Controllers\ElaboratiController;
use App\Scuola\Controllers\ElaboratiMediaController;
use App\Scuola\Controllers\ElaboratiStudentsController;
use App\Scuola\Controllers\ScuolaController;
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
    Route::get('risorse', [RisorsaController::class, 'index']);
    Route::get('logs', [LogsActivityController::class, 'index'])->name('admin.logs');
});

Route::prefix('nomadelfia')->middleware('auth')->name('nomadelfia.')->group(function () {
    Route::get('/', [PopolazioneSummaryController::class, 'index'])->middleware('can:popolazione.persona.visualizza')->name('index');

    // TODO: move 'create' and 'store' action into a dedicated controller because they perfrom different actions and not create and store new persona
    Route::get('persone-new', [PersoneController::class, 'create'])->middleware('can:popolazione.persona.inserisci')->name('persone.create');
    Route::post('persone', [PersoneController::class, 'store'])->middleware('can:popolazione.persona.inserisci')->name('persone.store');
    Route::get('persone/{idPersona}', [PersoneController::class, 'show'])->middleware('can:popolazione.persona.visualizza')->name('persone.dettaglio');
    Route::delete('persone/{idPersona}', [PersoneController::class, 'delete'])->middleware('can:popolazione.persona.elimina')->name('persone.delete');
    Route::get('persone/anagrafica/new', [PersonaAnagraficaController::class, 'create'])->name('persone.anagrafica.create');
    Route::post('persone/anagrafica', [PersonaAnagraficaController::class, 'store'])->name('persone.anagrafica.store');
    Route::get('persone/{idPersona}/anagrafica', [PersonaAnagraficaController::class, 'edit'])->name('persone.anagrafica.edit');
    Route::put('persone/{idPersona}/anagrafica', [PersonaAnagraficaController::class, 'update'])->name('persone.anagrafica.update');
    Route::get('persone/{idPersona}/entrata/scelta', [PersonaEntrataController::class, 'create'])->name('persone.inserimento.entrata.scelta.view');
    Route::post('persone/{idPersona}/entrata/scelta', [PersonaEntrataController::class, 'store'])->name('persone.inserimento.entrata.scelta');
    Route::post('persone/{idPersona}/entrata/{entrata}/modifica', [PersonaEntrataController::class, 'update'])->name('persone.dataentrata.modifica');
    Route::post('persone/{idPersona}/decesso', [PersonaDecessoController::class, 'store'])->name('persone.decesso');
    Route::post('persone/{idPersona}/uscita', [PersonaUscitaController::class, 'store'])->name('persone.uscita');
    Route::post('persone/{idPersona}/uscita/{uscita}/modifica', [PersonaUscitaController::class, 'update'])->name('persone.datauscita.modifica');
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
    Route::post('aziende/{id}/assegna', [AziendeController::class, 'assegnaPersona'])->name('azienda.assegna');
    Route::post('aziende/{id}/sposta/{idPersona}', [AziendeController::class, 'spostaPersona'])->name('azienda.sposta');

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
    Route::get('popolazione/stampa/preview', [PopolazioneNomadelfiaController::class, 'preview'])->name('popolazione.anteprima');

    Route::get('popolazione/', [PopolazioneNomadelfiaController::class, 'show'])->name('popolazione');
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

});

Route::prefix('biblioteca')->middleware('auth')->group(function () {
    Route::view('/', 'biblioteca.index')->withoutMiddleware('auth')->name('biblioteca');
    Route::get('libri', [LibriController::class, 'showSearchLibriForm'])->withoutMiddleware('auth')->name('libri.ricerca');
    Route::get('libri/ricerca', [LibriController::class, 'searchConfirm'])->withoutMiddleware('auth')->name('libri.ricerca.submit');
    Route::get('etichette/preview', [EtichetteController::class, 'preview'])->withoutMiddleware('auth')->name('libri.etichette.preview');

    Route::get('libri/{idLibro}/collocazione', [LibriController::class, 'showEditCollocazioneForm'])->middleware('can:biblioteca.libro.visualizza')->name('libro.collocazione');
    Route::post('libri/{idLibro}/collocazione/update', [LibriController::class, 'updateCollocazione'])->middleware('can:biblioteca.libro.modifica')->name('libro.collocazione.update');
    Route::post('libri/{idLibro}/confirm', [LibriController::class, 'confirmCollocazione'])->middleware('can:biblioteca.libro.modifica')->name('libro.collocazione.update.confirm');
    Route::get('libri/inserimento', [LibriController::class, 'showInsertLibroForm'])->middleware('can:biblioteca.libro.inserisci')->name('libri.inserisci');
    Route::post('libri/inserimento', [LibriController::class, 'insertConfirm'])->middleware('can:biblioteca.libro.inserisci')->name('libri.inserisci.Confirm');

    Route::get('libri/prestiti', [LibriPrestitiController::class, 'view'])->middleware('can:biblioteca.libro.prenota')->name('libri.prestiti');
    Route::get('libri/prestiti/ricerca', [LibriPrestitiController::class, 'search'])->middleware('can:biblioteca.libro.visualizza')->name('libri.prestiti.ricerca');
    Route::get('libri/prestiti/{idPrestito}', [LibriPrestitiController::class, 'show'])->middleware('can:biblioteca.libro.visualizza')->name('libri.prestito');
    Route::get('libri/prestiti/{idPrestito}/modifica', [LibriPrestitiController::class, 'edit'])->middleware('can:biblioteca.libro.modifica')->name('libri.prestito.modifica'); //->middleware('can:edit,App\Libro')->
    Route::post('libri/prestiti/{idPrestito}/modifica', [LibriPrestitiController::class, 'editConfirm'])->middleware('can:biblioteca.libro.modifica'); //->name('libri.prestito.modificaConfirm');
    Route::post('libri/prestiti/{idPrestito}/concludi', [LibriPrestitiController::class, 'conclude'])->middleware('can:biblioteca.libro.prenota')->name('libri.prestito.concludi');

    Route::get('libri/eliminati', [LibriController::class, 'showDeleted'])->name('libri.eliminati');
    Route::get('libri/{idLibro}', [LibriController::class, 'show'])->middleware('can:biblioteca.libro.prenota')->name('libro.dettaglio');
    Route::get('libri/{idLibro}/modifica', [LibriController::class, 'edit'])->middleware('can:biblioteca.libro.modifica')->name('libro.modifica');
    Route::post('libri/{idLibro}/modifica', [LibriController::class, 'editConfirm'])->middleware('can:biblioteca.libro.modifica');
    Route::get('libri/{idLibro}/elimina', [LibriController::class, 'delete'])->middleware('can:biblioteca.libro.elimina')->name('libro.elimina');
    Route::post('libri/{idLibro}/elimina', [LibriController::class, 'deleteConfirm'])->middleware('can:biblioteca.libro.elimina');
    Route::get('libri/{idLibro}/prenota', [LibriController::class, 'book'])->middleware('can:biblioteca.libro.prenota')->name('libri.prenota');
    Route::post('libri/{idLibro}/prenota', [LibriController::class, 'bookConfirm'])->middleware('can:biblioteca.libro.prenota');
    Route::post('libri/{idLibro}/ripristina', [LibriController::class, 'restore'])->middleware('can:biblioteca.libro.elimina')->name('libri.ripristina');

    Route::get('etichette', [EtichetteController::class, 'view'])->middleware('can:biblioteca.etichetta.visualizza')->name('libri.etichette');
    Route::post('etichette', [EtichetteController::class, 'etichetteFromToCollocazione'])->middleware('can:biblioteca.etichetta.visualizza')->name('libri.etichette.aggiungi');
    Route::post('etichette/add/{idLibro}', [EtichetteController::class, 'addLibro'])->middleware('can:biblioteca.etichetta.inserisci')->name('libri.etichette.aggiungi.libro');
    Route::post('etichette/remove', [EtichetteController::class, 'removeAll'])->middleware('can:biblioteca.etichetta.elimina')->name('libri.etichette.rimuovi');
    Route::post('etichette/remove/{idLibro}', [EtichetteController::class, 'removeLibro'])->middleware('can:biblioteca.etichetta.elimina')->name('libri.etichette.rimuovi.libro');
    Route::get('etichette/print', [EtichetteController::class, 'printToPdf'])->middleware('can:biblioteca.etichetta.visualizza')->name('libri.etichette.stampa');

    Route::group([
        'middleware' => [
            'can:biblioteca.autore.inserisci',
            'can:biblioteca.autore.visualizza',
        ],
    ], function () {
        Route::get('autori/search', [AutoriController::class, 'search'])->name('autori.ricerca');
        Route::resource('autori', AutoriController::class);
    });

    Route::group([
        'middleware' => [
            'can:biblioteca.autore.visualizza',
            'can:biblioteca.autore.inserisci',
        ],
    ], function () {
        Route::get('editori/search', [EditoriController::class, 'search'])->name('editori.ricerca');
        Route::resource('editori', EditoriController::class);
    });

    Route::group(['middleware' => ['can:biblioteca.libro.visualizza']], function () {
        Route::resource('classificazioni', ClassificazioniController::class);
    });

    Route::get('video', [VideoController::class, 'showSearchVideoForm'])->name('video');
    Route::get('video/search', [VideoController::class, 'searchConfirm'])->name('video.ricerca.submit');
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
    Route::get('veicoli/modifica/{id}', [VeicoliController::class, 'edit'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.modifica');
    Route::post('veicoli/modifica/{id}', [VeicoliController::class, 'editConfirm'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.modifica.confirm');
    Route::delete('demolisci/veicolo', [VeicoliController::class, 'demolisci'])->middleware('can:meccanica.veicolo.modifica')->name('veicoli.demolisci');

    Route::post('filtro/aggiungi', [VeicoliController::class, 'aggiungiFiltro'])->middleware('can:meccanica.veicolo.modifica')->name('filtri.aggiungi');
    Route::view('filtri', 'officina.gestione.filtri')->middleware('can:meccanica.veicolo.modifica')->name('filtri');
    Route::post('olio/aggiungi', [VeicoliController::class, 'aggiungiOlio'])->middleware('can:meccanica.veicolo.modifica')->name('olio.aggiungi');
    Route::get('/patenti', [PatentiController::class, 'patenti'])->middleware('can:meccanica.veicolo.visualizza')->name('officina.patenti');
});

Route::prefix('patente')->middleware('auth')->group(function () {
    Route::get('/', [PatenteController::class, 'scadenze'])->middleware('can:scuolaguida.patente.visualizza')->name('patente.scadenze');
    Route::get('/ricerca', [PatenteController::class, 'patente'])->middleware('can:scuolaguida.patente.visualizza')->name('patente.ricerca');

    Route::get('/elenchi', [PatenteController::class, 'elenchi'])->middleware('can:scuolaguida.patente.visualizza')->name('patente.elenchi');
    Route::get('/elenchi/stampa', [PatenteController::class, 'stampaAutorizzati'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.autorizzati.esporta.pdf');
    Route::get('/elenchi/preview', [PatenteController::class, 'stampaAutorizzatiPreview'])->name('patente.elenchi.autorizzati.esporta.preview');
    Route::get('/elenchi/esporta/excel', [PatenteController::class, 'autorizzatiEsportaExcel'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.autorizzati.esporta.excel');
    Route::get('/elenchi/patenti/pdf', [PatenteController::class, 'esportaPatentiPdf'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.patenti.esporta.pdf');
    Route::get('/elenchi/patenti/excel', [PatenteController::class, 'esportaPatentiExcel'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.patenti.esporta.excel');
    Route::get('/elenchi/cqc/excel', [PatenteController::class, 'esportaCQCExcel'])->middleware('can:scuolaguida.patente.esporta')->name('patente.elenchi.cqc.esporta.excel');

    Route::get('/search', [PatenteController::class, 'ricerca'])->name('patente.ricerca.conferma');
    Route::get('modifica/{id}', [PatenteController::class, 'modifica'])->middleware('can:scuolaguida.patente.modifica')->name('patente.modifica');
    Route::get('elimina/{id}', [PatenteController::class, 'elimina'])->middleware('can:scuolaguida.patente.elimina')->name('patente.elimina');
    Route::get('inserimento', [PatenteController::class, 'inserimento'])->middleware('can:scuolaguida.patente.inserisci')->name('patente.inserimento');
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

Route::view('/meteo', 'stazionemeteo.index')->name('stazionemeteo');

Route::get('/debug-sentry', function () {
    throw new Exception('A fake sentry error!');
});
