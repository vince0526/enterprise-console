<?php

declare(strict_types=1);

namespace App\Services\Database;

use App\Models\DatabaseConnection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

final class DynamicConnector implements DynamicConnectorInterface
{
    private const NAME = 'ec_dynamic';

    public function connect(DatabaseConnection $conn): ConnectionInterface
    {
        $cfg = [
            'driver' => $conn->driver,          // mysql|pgsql|sqlsrv|sqlite
            'host' => $conn->host,
            'port' => $conn->port,
            'database' => $conn->database,
            'username' => $conn->username,
            'password' => $conn->password,        // TODO: decrypt if encrypted
            'charset' => 'utf8mb4',
            'prefix' => '',
        ];

        if ($conn->driver === 'pgsql') {
            $cfg['sslmode'] = 'prefer';
        }

        if ($conn->driver === 'sqlite') {
            if ($conn->database === ':memory:') {
                // For in-memory sqlite, reuse the default connection so the test
                // harness and dynamic connector operate on the same in-memory
                // database instance and migrations only run once.
                return DB::connection();
            }

            if (! $conn->database) {
                $cfg['database'] = database_path('external.sqlite');
            } else {
                $cfg['database'] = $conn->database;
            }
        }

        Config::set('database.connections.'.self::NAME, $cfg);

        return DB::connection(self::NAME);
    }
}
