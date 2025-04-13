<?php

declare(strict_types=1);

use App\Photo\Models\Photo;

use function PHPUnit\Framework\assertEquals;

it('add photos to db', function (): void {

    Photo::factory(3)
        ->inFolder('2023-14-05 AAAXXX A beatiful moment')
        ->create();

    $c = Photo::where('folder_title', 'like', '%A beatiful moment%')->count();

    assertEquals(3, $c);
});
