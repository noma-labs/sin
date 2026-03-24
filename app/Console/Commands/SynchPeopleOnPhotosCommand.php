<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class SynchPeopleOnPhotosCommand extends Command
{
    protected $signature = 'photos:sync-people';

    protected $description = 'Update persona_id in photos_people table based on persona_nome';

    public function handle(): int
    {
        $this->info('Updating persona_id in photos_people...');

        $updated = DB::connection('db_foto')->update(<<<'SQL'
            UPDATE db_foto.photos_people pp
            LEFT JOIN db_nomadelfia.alfa_enrico_15_feb_23 e ON e.FOTO = pp.persona_nome
            LEFT JOIN db_nomadelfia.persone p ON p.id_alfa_enrico = e.id
            SET pp.persona_id = p.id
            WHERE pp.persona_id IS NULL
        SQL);

        $this->info('Update complete. Rows affected: '.$updated);

        return self::SUCCESS;
    }
}
