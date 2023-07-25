<?php


use Domain\Photo\Models\Photo;
use function Pest\Laravel\assertDatabaseHas;

it('add photos to db', function () {
    Photo::factory(5)
        ->inFolder('2023-14-05 AAAXXX A beatiful moment')
        ->create();

    $this->assertDatabaseCount('photos', 5, 'db_foto');
});