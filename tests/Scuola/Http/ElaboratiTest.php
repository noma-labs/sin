<?php

namespace Tests\Http\Scuola;

use App\Scuola\Controllers\ElaboratiController;
use App\Scuola\Models\Elaborato;
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

    $updateData = [
        'titolo' => 'Updated Title',
        'anno_scolastico' => '2015/2016',
    ];

    login();
    $this->put(action([ElaboratiController::class, 'update'], $elaborato->id), $updateData)
        ->assertRedirect();

    $this->assertDatabaseHas('elaborati', [
        'id' => $elaborato->id,
        'titolo' => 'Updated Title',
        'anno_scolastico' => '2015/2016',
    ], 'db_scuola');
});

it('can download a pdf file', function (): void {
    Storage::fake('scuola');
    $faker = Faker::create();
    $documentName = $faker->unique()->word.'.pdf';

    $file = UploadedFile::fake()->create($documentName, 100, 'application/pdf');

    $filePath = $file->storeAs('elaborati', $documentName, 'scuola');

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

    $this->assertTrue(Storage::disk('scuola')->exists($filePath));
});
