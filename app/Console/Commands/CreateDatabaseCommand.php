<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateDatabaseCommand extends Command
{
    protected $signature = 'make:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the database listed int config database file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $connections = collect(config('database.connections'))
                ->except(['information_schema'])
                ->keys();

            foreach ($connections as $connection) {
                // Skip connections that do not start with 'db_' or 'archivio_'
                if (! Str::startsWith($connection, 'db_') && ! Str::startsWith($connection, 'archivio_')) {
                    continue;
                }
                $dbName = config("database.connections.{$connection}.database");
                // to create database we have to connect to an existing db
                // use the `information_schema` database as base connection because it is always created by mysql
                DB::connection('information_schema')->unprepared("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
                $this->info("Created  database '$dbName' for '$connection' connection");
            }

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
