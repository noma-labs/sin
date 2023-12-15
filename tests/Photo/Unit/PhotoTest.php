<?php

use Domain\Photo\Models\Photo;
use function PHPUnit\Framework\assertEquals;

it('add photos to db', function () {

    Photo::factory(3)
        ->inFolder('2023-14-05 AAAXXX A beatiful moment')
        ->create();

    $c = Photo::where('folder_title', 'like', '%A beatiful moment%')->count();

    assertEquals(3, $c);
});
