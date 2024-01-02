<?php

it('run exif extract command with a folder', function () {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Exif/testfile'])->assertSuccessful();
});

it('run exif extract command with a file', function () {
    $this->artisan('exif:extract', ['path' => 'tests/Photo/Exif/testfile/BlueSquare.jpg'])->assertSuccessful();
});


//it('run exif import command', function () {
//    $this->artisan('exif:import ', ['file' => 'tests/Photo/Exif/testfile/BlueSquare.jpg'])->assertSuccessful();
//});
