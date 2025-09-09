<?php

declare(strict_types=1);

namespace App\Services\Database;

use App\Models\DatabaseConnection;

interface DynamicConnectorInterface
{
    /**
     * Return a database connection instance for the provided DatabaseConnection.
     * The return type is intentionally untyped to allow passing either a
     * \Illuminate\Database\Connection or compatible object in tests.
     *
     * @return mixed
     */
    public function connect(DatabaseConnection $connection);
}
