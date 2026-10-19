<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const string OLD_SUBJECT_TYPE = 'App\Nomadelfia\Persona\Models\Persona';

    private const string NEW_SUBJECT_TYPE = 'App\Anagrafe\Persona\Models\Persona';

    public $connection = 'db_auth';

    public function up(): void
    {
        DB::connection($this->connection)
            ->table(config('activitylog.table_name'))
            ->where('subject_type', self::OLD_SUBJECT_TYPE)
            ->update(['subject_type' => self::NEW_SUBJECT_TYPE]);
    }

    public function down(): void
    {
        DB::connection($this->connection)
            ->table(config('activitylog.table_name'))
            ->where('subject_type', self::NEW_SUBJECT_TYPE)
            ->update(['subject_type' => self::OLD_SUBJECT_TYPE]);
    }
};
