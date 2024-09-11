<?php

namespace App\Providers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Concerns\TestDatabases;

// Reference: https://sarahjting.com/blog/laravel-paratest-multiple-db-connections
class ParallelTestingExtendedServiceProvider extends ServiceProvider
{
    public function boot(){
            ParallelTesting::setUpProcess(function ($token) {
                echo "Process setup for token: $token" . PHP_EOL;
            });

            ParallelTesting::setUpTestCase(function ($token, $testCase) {
                echo "SetupTest token: $token" . PHP_EOL;
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
