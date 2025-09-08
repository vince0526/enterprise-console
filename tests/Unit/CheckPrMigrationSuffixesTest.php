<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class CheckPrMigrationSuffixesTest extends TestCase
{
    public function test_harness_detects_collisions()
    {
        $event = __DIR__.'/../../scripts/test-pr-event-rich.json';
        $harness = __DIR__.'/../../scripts/check-pr-migration-suffixes.harness.php';
        $outputFile = __DIR__.'/../../scripts/check-pr-migration-suffixes.harness.output.json';

        // Ensure old output removed
        @unlink($outputFile);

        // Run harness
        exec("php -f \"$harness\" \"$event\" 2>&1", $out, $code);

        // Harness should exit with non-zero if collisions exist
        $this->assertNotEquals(0, $code, 'Harness should detect collisions and exit non-zero');

        $this->assertFileExists($outputFile, 'Harness output file must exist');

        $data = json_decode(file_get_contents($outputFile), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('collisions', $data);
        $this->assertNotEmpty($data['collisions'], 'Expected collisions in the test harness output');

        // Simple edge assert that create_users_table is present
        $this->assertArrayHasKey('create_users_table', $data['collisions']);
    }
}
