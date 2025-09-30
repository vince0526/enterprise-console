<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CoreDatabase;
use App\Models\CoreDatabaseLifecycleEvent;
use App\Models\CoreDatabaseLink;
use App\Models\CoreDatabaseOwner;
use App\Models\DatabaseConnection;
use Illuminate\Database\Seeder;

class CoreSubmodulesSeeder extends Seeder
{
    public function run(): void
    {
        $core = CoreDatabase::first();
        if (! $core) {
            $core = CoreDatabase::create([
                'name' => 'Core Identity',
                'environment' => 'Production',
                'platform' => 'PostgreSQL',
                'owner' => 'Platform Team',
                'lifecycle' => 'Long-lived',
                'status' => 'healthy',
            ]);
        }

        // Owners
        CoreDatabaseOwner::firstOrCreate([
            'core_database_id' => $core->id,
            'owner_name' => 'Platform Team',
        ], [
            'role' => 'Technical Owner',
            'effective_date' => now()->subMonths(6)->toDateString(),
        ]);

        // Lifecycle events
        CoreDatabaseLifecycleEvent::firstOrCreate([
            'core_database_id' => $core->id,
            'event_type' => 'Created',
        ], [
            'details' => 'System went live.',
            'effective_date' => now()->subYear()->toDateString(),
        ]);

        // Link to a connection if available
        $conn = DatabaseConnection::first();
        CoreDatabaseLink::firstOrCreate([
            'core_database_id' => $core->id,
            'linked_connection_name' => $conn->name ?? 'prod-identity',
        ], [
            'database_connection_id' => $conn?->id,
            'link_type' => 'Primary',
        ]);
    }
}
