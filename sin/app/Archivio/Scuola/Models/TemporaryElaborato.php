<?php

namespace App\Scuola\Models;

use Spatie\MediaLibraryPro\Models\TemporaryUpload;

class TemporaryElaborato extends TemporaryUpload
{
    protected $connection = 'db_scuola';
    protected $table = "temporary_uploads";

}