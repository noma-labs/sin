<?php

declare(strict_types=1);

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\CoverImageController;
use App\Scuola\Controllers\ElaboratiController;
use App\Scuola\Models\Elaborato;
use App\Scuola\Models\Studente;
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

it('can update an elaborato', function (): void {
    $elaborato = Elaborato::factory()->create();

    login();

    $this->put(action([ElaboratiController::class, 'update'], $elaborato->id), [
        'titolo' => 'Updated Title',
        'anno_scolastico' => '2015/2016',
    ])->assertRedirect();

    $this->assertDatabaseHas('elaborati', [
        'id' => $elaborato->id,
        'titolo' => 'Updated Title',
        'anno_scolastico' => '2015/2016',
    ], 'db_scuola');
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

it('can store an elaborato', function (): void {
    login();

    $s = Studente::factory()->minorenne()->maschio()->create();

    $this->post(action([ElaboratiController::class, 'store']), [
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/ 2016',
        'studenti_ids' => [$s->id],
        'coordinatori_ids' => [],
        'dimensione' => 'A4',
        'file' => UploadedFile::fake()->create('test.pdf'),
    ])->assertRedirectToRoute('scuola.elaborati.index');

    $this->assertDatabaseHas('elaborati', [
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/2016',
    ], 'db_scuola');
});

it('return a flash error id dimension is wrong', function (): void {
    login();

    $s = Studente::factory()->minorenne()->maschio()->create();

    $this->post(action([ElaboratiController::class, 'store']), [
        'titolo' => 'Test Title',
        'anno_scolastico' => '2015/ 2016',
        'studenti_ids' => [$s->id],
        'coordinatori_ids' => [],
        'dimensione' => '2323',
        'file' => UploadedFile::fake()->create('test.pdf'),
    ])->assertSessionHasErrors(['dimensione' => 'La dimesione `2323` non è valida. La dimensione deve essere nella forma LxH in millimetri. Per esempio: 210x297']);

});
