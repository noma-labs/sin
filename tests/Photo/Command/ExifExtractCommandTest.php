<?php

it('run exif extract command with a folder', function () {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Exif/testfile'])->assertSuccessful();
});

it('run exif extract command with a file', function () {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Exif/testfile/BlueSquare.jpg'])->assertSuccessful();
});
