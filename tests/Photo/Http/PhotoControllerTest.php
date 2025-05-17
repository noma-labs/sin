<?php

declare(strict_types=1);

namespace Tests\Http\Nomadelfia;

use App\Photo\Controllers\PhotoController;
use App\Photo\Models\Photo;

it('indes the photos', function (): void {
    $photo = Photo::factory()->create();

    login();

    $this->get(action([PhotoController::class, 'index']))
        ->assertSuccessful()
        ->assertSee($photo->taken_at->format('d/m/Y'));
});
