<?php

namespace App\Providers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Concerns\TestDatabases;

// Reference: https://sarahjting.com/blog/laravel-paratest-multiple-db-connections
class ParallelTestingExtendedServiceProvider extends ServiceProvider
{
    // ORDER is important
    var $databaseMigrationMap = [
        "db_auth" => 'database/migrations/admsys',
        "db_nomadelfia" => 'database/migrations/db_nomadelfia',
        "db_scuola" => 'database/migrations/scuola',
        "db_biblioteca" => 'database/migrations/biblioteca',
        "db_patente" => 'database/migrations/patente',
        "db_officina" => 'database/migrations/officina',
        "db_foto" => 'database/migrations/foto',
        "db_rtn" => 'database/migrations/rtn',
    ];

    public function boot(){
            ParallelTesting::setUpProcess(function ($token) {

                foreach ($this->databaseMigrationMap as $connection => $path) {
                    $dbName = config("database.connections.{$connection}.database");

                    $name = $dbName."_dido_".$token;

                    echo "Creating database '$name' for '$connection' connection with origin name '$dbName'" . PHP_EOL;

                    Schema::connection('information_schema')->dropDatabaseIfExists($name);
                    Schema::connection('information_schema')->createDatabase($name);


                    DB::purge();
                    config()->set(
                        "database.connections.{$connection}.database",
                        $name,
                    );
                    Artisan::call('migrate:fresh', ['--database' => $connection, '--path' => $path]);

                }
            });

            ParallelTesting::setUpTestCase(function ($token, $testCase) {
                foreach ($this->databaseMigrationMap as $connection => $path) {
                    $dbName = config("database.connections.{$connection}.database");

                    $name = $dbName."_dido_".$token;


                    DB::purge();
                    config()->set(
                        "database.connections.{$connection}.database",
                        $name,
                    );
                    Artisan::call('migrate:fresh', ['--database' => $connection, '--path' => $path]);

                }
            });

            // Executed when a test database is created...
            ParallelTesting::setUpTestDatabase(function ($database, $token) {
                echo "setUpTestDatabase token: $token $database" . PHP_EOL;
            });

            ParallelTesting::tearDownTestCase(function ($token, $testCase) {
                echo "tearDownTestCase token: $token" . PHP_EOL;
            });

            ParallelTesting::tearDownProcess(function ($token) {
                echo "tearDownProcess token: $token" . PHP_EOL;

            });
    }

    /**
     * Runs the given callable using the given database.
     *
     * @param  string  $database
     * @param  callable  $callable
     * @return void
     */
    protected function usingDatabase($database, $callable)
    {
        $original = DB::getConfig('database');

        try {
            $this->switchToDatabase($database);
            $callable();
        } finally {
            $this->switchToDatabase($original);
        }
    }

    /**
     * Switch to the given database.
     *
     * @param  string  $database
     * @return void
     */
    protected function switchToDatabase($database)
    {
        DB::purge();

        $default = config('database.default');

        $url = config("database.connections.{$default}.url");

        if ($url) {
            config()->set(
                "database.connections.{$default}.url",
                preg_replace('/^(.*)(\/[\w-]*)(\??.*)$/', "$1/{$database}$3", $url),
            );
        } else {
            config()->set(
                "database.connections.{$default}.database",
                $database,
            );
        }
    }

    // use TestDatabases;

    // protected array $parallelConnections = ['db_auth', 'db_nomadelfia']; //, 'db_scuola', 'db_biblioteca', 'db_patente', 'db_officina', 'db_foto', 'db_rtn'];


    // public function boot()
    // {
    //     echo "[EXTENDED]: booting". PHP_EOL;
    //     if ($this->app->runningInConsole()) {
    //         $this->bootTestDatabase();
    //     }
    // }

    // protected function bootTestDatabase()
    // {
    //     echo "[EXTENDED]: bootTestDatabase". PHP_EOL;
    //     ParallelTesting::setUpProcess(function () {
    //         if (ParallelTesting::option('recreate_databases')) {
    //                 foreach ($this->parallelConnections as $connection) {
    //                     echo "[EXTENDED] Dropping ".$this->testDatabaseOnConnection($connection) . PHP_EOL;
    //                     Schema::connection($connection)
    //                         ->dropDatabaseIfExists(
    //                             $this->testDatabaseOnConnection($connection)
    //                         );
    //                 }
    //         }
    //     });

    //     ParallelTesting::setUpTestCase(function ($testCase) {
    //         $uses = array_flip(class_uses_recursive(get_class($testCase)));

    //         $databaseTraits = [
    //             DatabaseMigrations::class,
    //             DatabaseTransactions::class,
    //             RefreshDatabase::class,
    //         ];

    //         if (Arr::hasAny($uses, $databaseTraits) && ! ParallelTesting::option('without_databases')) {
    //             $allCreated = [];

    //             foreach ($this->parallelConnections as $connection) {
    //                 $this->usingConnection($connection, function ($connection) use (&$allCreated) {
    //                     $database = config("database.connections.{$connection}.database");
    //                     echo "[EXTENDED] creating ".$database . PHP_EOL;

    //                     [$testDatabase, $created] = $this->ensureTestDatabaseExists($database);
    //                     echo "[EXTENDED] switching to  ".$testDatabase . PHP_EOL;

    //                     $this->switchToDatabase($testDatabase);
    //                     echo "[EXTENDED] switch ok ".$testDatabase . " created: ". $created. PHP_EOL;
    //                     if ($created) {
    //                         echo "[EXTENDED] creating ".$testDatabase . PHP_EOL;
    //                         $allCreated[] = [$connection, $testDatabase];
    //                         echo "[EXTENDED] append ok ".$testDatabase . " ". $connection. PHP_EOL;
    //                     }
    //                 });
    //             }

    //             if (isset($uses[DatabaseTransactions::class])) {
    //                 $this->ensureSchemaIsUpToDate();
    //             }

    //             echo "[EXTENDED] each created ...".  PHP_EOL;

    //             foreach ($allCreated as [$connection, $testDatabase]) {
    //                 $this->usingConnection($connection, function () use ($testDatabase) {
    //                     ParallelTesting::callSetUpTestDatabaseCallbacks($testDatabase);
    //                 });
    //             }
    //             echo "[EXTENDED] created OK".  PHP_EOL;
    //         }
    //     });

    //     ParallelTesting::tearDownProcess(function () {
    //         if (ParallelTesting::option('drop_databases')) {
    //             foreach ($this->parallelConnections as $connection) {
    //                 echo "[EXTENDED] dropping ...".  PHP_EOL;
    //                 Schema::connection($connection)
    //                     ->dropDatabaseIfExists(
    //                         $this->testDatabaseOnConnection($connection)
    //                     );

    //                 echo "[EXTENDED] dropped ...".  PHP_EOL;
    //             }
    //         }
    //     });
    // }

    // protected function usingConnection(string $connection, \Closure $callable): void
    // {
    //     $originalConnection = config("database.default");

    //     try {
    //         config()->set("database.default", $connection);
    //         $callable($connection);
    //     } finally {
    //         config()->set("database.default", $originalConnection);
    //     }
    // }

    // protected function testDatabaseOnConnection(string $connection): string
    // {
    //     return $this->testDatabase(config("database.connections.{$connection}.database"));
    // }
}
