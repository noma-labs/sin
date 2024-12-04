<?php

declare(strict_types=1);

it('run exif extract command with a folder', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Exif/testfile'])->assertSuccessful();
});

it('run exif extract command with a file', function (): void {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Exif/testfile/BlueSquare.jpg'])->assertSuccessful();
});
