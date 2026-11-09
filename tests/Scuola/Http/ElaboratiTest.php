<?php

declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Biblioteca\Models\Libro;
use App\Scuola\Controllers\CoverImageController;
use App\Scuola\Controllers\ElaboratiController;
use App\Scuola\Controllers\ElaboratiMediaController;
use App\Scuola\Models\ArgomentoElaborato;
use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;
use App\Scuola\Models\TipoElaborato;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('can show elaborati index ', function (): void {
    login();
    $this->get(action([ElaboratiController::class, 'index']))
        ->assertSuccessful();
});

it('can show an elaborato', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();
    $this->get(action([ElaboratiController::class, 'show'], $elaborato->id))
        ->assertStatus(200);
});

it('returns not found when showing an elaborato that does not exist', function (): void {
    login();

    $this->get(action([ElaboratiController::class, 'show'], 0))
        ->assertNotFound();
});

it('shows the details of an elaborato', function (): void {
    $elaborato = Elaborato::factory()->create([
        'titolo' => 'La storia del villaggio',
        'collocazione' => 'SPA001',
        'anno_scolastico' => '2015/2016',
        'classi' => '1 media,2 media',
        'tipo' => TipoElaborato::CompitoInClasse,
        'rilegatura' => 'Spirale',
    ]);
    ArgomentoElaborato::factory()->for($elaborato)->create(['argomento' => 'Il Risorgimento', 'materia' => 'Storia']);
    ArgomentoElaborato::factory()->for($elaborato)->create(['argomento' => 'La fotosintesi', 'materia' => 'Scienze']);
    ArgomentoElaborato::factory()->for($elaborato)->create(['argomento' => 'Le piante', 'materia' => 'Scienze']);
    ArgomentoElaborato::factory()->for($elaborato)->create(['argomento' => 'Gita a Roma', 'materia' => null]);

    login();

    $this->get(action([ElaboratiController::class, 'show'], $elaborato->id))
        ->assertOk()
        ->assertSee('La storia del villaggio')
        ->assertSee('SPA001')
        ->assertSee('2015/2016')
        ->assertSee('1 media, 2 media')
        ->assertSee('Compito in classe')
        ->assertSee('Scienze, Storia')
        ->assertSeeInOrder(['Gita a Roma', 'La fotosintesi', 'Le piante', 'Il Risorgimento'])
        ->assertSee('Spirale');
});

it('offers the preview and the download of an elaborato with a file', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();

    $this->get(action([ElaboratiController::class, 'show'], $elaborato->id))
        ->assertOk()
        ->assertSee(route('scuola.elaborati.preview', $elaborato->id))
        ->assertSee(route('scuola.elaborati.download', $elaborato->id))
        ->assertSee(route('scuola.elaborati.media.destroy', $elaborato->id))
        ->assertDontSee(route('scuola.elaborati.media.store', $elaborato->id));
});

it('offers the upload instead of the download of an elaborato without a file', function (): void {
    $elaborato = Elaborato::factory()->create(['file_path' => null]);

    login();

    $this->get(action([ElaboratiController::class, 'show'], $elaborato->id))
        ->assertOk()
        ->assertSee(route('scuola.elaborati.media.store', $elaborato->id))
        ->assertDontSee(route('scuola.elaborati.download', $elaborato->id))
        ->assertDontSee(route('scuola.elaborati.preview', $elaborato->id));
});

it('tells when an elaborato has no studenti and no coordinatori', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();

    $this->get(action([ElaboratiController::class, 'show'], $elaborato->id))
        ->assertOk()
        ->assertSee('Nessuno studente.')
        ->assertSee('Nessun coordinatore.');
});

it('links an elaborato to its libro', function (): void {
    $libro = Libro::factory()->create();
    $elaborato = Elaborato::factory()->create(['libro_id' => $libro->id]);

    login();

    $this->get(action([ElaboratiController::class, 'show'], $elaborato->id))
        ->assertOk()
        ->assertSee(route('books.show', $libro->id));
});

it('rejects the upload of an elaborato without a file', function (): void {
    $elaborato = Elaborato::factory()->create(['file_path' => null]);

    login();

    $this->post(action([ElaboratiMediaController::class, 'store'], $elaborato->id))
        ->assertSessionHasErrors(['file' => 'Il file è obbligatorio.']);

    expect($elaborato->fresh()->file_path)->toBeNull();
});

it('can update an elaborato', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();

    $this->put(action([ElaboratiController::class, 'update'], $elaborato->id), [
        'titolo' => 'Updated Title',
        'anno_scolastico' => '2015/2016',
        'tipo' => 'tema',
    ])->assertRedirect();

    $this->assertDatabaseHas('elaborati', [
        'id' => $elaborato->id,
        'titolo' => 'Updated Title',
        'anno_scolastico' => '2015/2016',
        'tipo' => 'tema',
    ], 'db_scuola');
});

it('clears the tipo of an elaborato', function (): void {
    $elaborato = Elaborato::factory()->create(['tipo' => TipoElaborato::Tema]);

    login();

    $this->put(action([ElaboratiController::class, 'update'], $elaborato->id), [
        'titolo' => 'Updated Title',
        'anno_scolastico' => '2015/2016',
        'tipo' => '',
    ])->assertRedirect();

    expect($elaborato->refresh()->tipo)->toBeNull();
});

it('can download a pdf file', function (): void {
    Storage::fake('media_originals');
    $faker = Faker::create();
    $documentName = $faker->unique()->word.'.pdf';

    $file = UploadedFile::fake()->create($documentName, 100, 'application/pdf');

    $filePath = $file->storeAs('elaborati', $documentName, 'media_originals');

    $elaborato = Elaborato::factory()->create([
        'file_path' => $filePath,
        'file_mime_type' => 'application/pdf',
    ]);

    login();
    $this->withoutExceptionHandling();
    $this->get(action([ElaboratiController::class, 'download'], $elaborato->id))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'attachment; filename='.$documentName);

    $this->assertTrue(Storage::disk('media_originals')->exists($filePath));
});

it('deletes the file of an elaborato from the disk and from the db', function (): void {
    Storage::fake('media_originals');
    $filePath = UploadedFile::fake()->create('elaborato.pdf', 100, 'application/pdf')
        ->storeAs('elaborati/2016', 'SPA001_elaborato.pdf', 'media_originals');

    $elaborato = Elaborato::factory()->create([
        'file_path' => $filePath,
        'file_mime_type' => 'application/pdf',
        'file_size' => 102400,
        'file_hash' => hash('sha256', 'elaborato'),
    ]);

    login();

    $this->delete(action([ElaboratiMediaController::class, 'destroy'], $elaborato->id))
        ->assertRedirectToRoute('scuola.elaborati.show', $elaborato->id)
        ->assertSessionHas('success');

    Storage::disk('media_originals')->assertMissing($filePath);
    $elaborato->refresh();
    expect($elaborato->file_path)->toBeNull()
        ->and($elaborato->file_mime_type)->toBeNull()
        ->and($elaborato->file_size)->toBeNull()
        ->and($elaborato->file_hash)->toBeNull();
});

it('does not delete anything of an elaborato without a file', function (): void {
    $elaborato = Elaborato::factory()->create(['file_path' => null]);

    login();

    $this->delete(action([ElaboratiMediaController::class, 'destroy'], $elaborato->id))
        ->assertRedirect()
        ->assertSessionHas('error');
});

it('can upload a cover image', function (): void {
    login();

    Storage::fake('media_previews');

    $file = UploadedFile::fake()->image('cover.png');

    $elaborato = Elaborato::factory()->create();

    $this->post(action([CoverImageController::class, 'store'], $elaborato->id), [
        'file' => $file,
    ])->assertRedirectToRoute('scuola.elaborati.show', $elaborato->id);

    Storage::disk('media_previews')->assertExists($elaborato->cover_image_path);
});

function freeElaboratoCollocazione(): string
{
    do {
        $collocazione = 'SP'.fake()->randomLetter().fake()->numerify('###');
        $collocazione = mb_strtoupper($collocazione);
    } while (Libro::withTrashed()->where('collocazione', $collocazione)->exists() || Elaborato::query()->where('collocazione', $collocazione)->exists());

    return $collocazione;
}

it('can store an elaborato also as a libro', function (): void {
    login();

    $s = Studente::factory()->minorenne()->maschio()->create();
    $collocazione = freeElaboratoCollocazione();

    $response = $this->post(action([ElaboratiController::class, 'store']), [
        'collocazione' => $collocazione,
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/ 2016',
        'studenti_ids' => [$s->id],
        'coordinatori_ids' => [],
        'dimensione' => 'A4',
        'tipo' => 'poesia',
    ]);

    $elaborato = Elaborato::query()->where('collocazione', $collocazione)->firstOrFail();
    $response->assertRedirectToRoute('scuola.elaborati.show', $elaborato->id);

    $libro = Libro::query()->where('collocazione', $collocazione)->firstOrFail();
    expect($libro->titolo)->toBe('TEST TITLE')
        ->and($libro->classificazione_id)->toBeNull()
        ->and($libro->autori()->pluck('autore')->all())->toBe(['SCUOLA NOMADELFIA'])
        ->and($libro->editori()->pluck('editore')->all())->toBe(['SCUOLA NOMADELFIA']);

    $this->assertDatabaseHas('elaborati', [
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/2016',
        'collocazione' => $collocazione,
        'libro_id' => $libro->id,
        'file_path' => null,
        'tipo' => 'poesia',
    ], 'db_scuola');
    expect($elaborato->studenti()->pluck('id')->all())->toBe([$s->id]);
});

it('can store an elaborato without studenti', function (): void {
    login();

    $collocazione = freeElaboratoCollocazione();

    $response = $this->post(action([ElaboratiController::class, 'store']), [
        'collocazione' => $collocazione,
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/2016',
    ]);

    $elaborato = Elaborato::query()->where('collocazione', $collocazione)->firstOrFail();
    $response->assertRedirectToRoute('scuola.elaborati.show', $elaborato->id);
    expect($elaborato->studenti()->count())->toBe(0)
        ->and($elaborato->tipo)->toBeNull();
});

it('rejects an elaborato with an unknown tipo', function (): void {
    login();

    $collocazione = freeElaboratoCollocazione();

    $this->post(action([ElaboratiController::class, 'store']), [
        'collocazione' => $collocazione,
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/2016',
        'tipo' => 'romanzo',
    ])->assertSessionHasErrors(['tipo' => 'Il tipo di elaborato non è valido.']);

    expect(Elaborato::query()->where('collocazione', $collocazione)->exists())->toBeFalse();
});

it('rejects an elaborato with a collocazione already used by a libro', function (): void {
    login();

    $s = Studente::factory()->minorenne()->maschio()->create();
    $collocazione = freeElaboratoCollocazione();
    Libro::factory()->create(['collocazione' => $collocazione]);

    $this->post(action([ElaboratiController::class, 'store']), [
        'collocazione' => $collocazione,
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/2016',
        'studenti_ids' => [$s->id],
    ])->assertSessionHasErrors(['collocazione' => 'La collocazione inserita esiste già.']);

    expect(Elaborato::query()->where('collocazione', $collocazione)->exists())->toBeFalse();
});

it('rejects an elaborato with a missing or malformed collocazione', function (?string $collocazione, string $message): void {
    login();

    $s = Studente::factory()->minorenne()->maschio()->create();

    $this->post(action([ElaboratiController::class, 'store']), [
        'collocazione' => $collocazione,
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/2016',
        'studenti_ids' => [$s->id],
    ])->assertSessionHasErrors(['collocazione' => $message]);
})->with([
    'missing' => [null, 'La collocazione è obbligatoria.'],
    'wrong prefix' => ['ABC001', 'La collocazione deve essere nella forma SP + lettera + 3 numeri (es. SPA001).'],
]);

it('return a flash error id dimension is wrong', function (): void {
    login();

    $s = Studente::factory()->minorenne()->maschio()->create();

    $this->post(action([ElaboratiController::class, 'store']), [
        'collocazione' => freeElaboratoCollocazione(),
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/ 2016',
        'studenti_ids' => [$s->id],
        'coordinatori_ids' => [],
        'dimensione' => '2323',
    ])->assertSessionHasErrors(['dimensione' => 'La dimesione `2323` non è valida. La dimensione deve essere nella forma LxH in millimetri. Per esempio: 210x297']);

});
