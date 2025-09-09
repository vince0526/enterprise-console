<?php

declare(strict_types=1);

// Harness for CI: runs collision detection using a local/mock files list and writes deterministic JSON output
// Usage: php scripts/check-pr-migration-suffixes.harness.php path/to/event.json

$eventPath = $argv[1] ?? null;
if (! $eventPath || ! file_exists($eventPath)) {
    fwrite(STDERR, "Provide path to event JSON as first argument\n");
    exit(2);
}
$payload = json_decode(file_get_contents($eventPath), true);
if (! is_array($payload)) {
    fwrite(STDERR, "Invalid JSON payload\n");
    exit(2);
}

// For deterministic behavior in CI harness, use a hard-coded files list representing the PR files
$files = [
    ['filename' => 'app/Models/User.php', 'status' => 'modified'],
    ['filename' => 'database/migrations/2025_09_09_000001_create_users_table.php', 'status' => 'added'],
    ['filename' => 'database/migrations/2025_09_09_000002_create_posts_table.php', 'status' => 'added'],
];

$addedFiles = [];
foreach ($files as $f) {
    if (! empty($f['filename']) && ($f['status'] ?? '') === 'added' && str_starts_with($f['filename'], 'database/migrations/')) {
        $addedFiles[] = $f['filename'];
    }
}

$migrationsDir = __DIR__.'/../database/migrations';
$existing = glob($migrationsDir.'/*.php') ?: [];

$existingSuffixes = [];
foreach ($existing as $file) {
    $base = basename($file, '.php');
    if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_(.+)$/', $base, $m)) {
        $existingSuffixes[$m[1]] = $file;
    } else {
        $existingSuffixes[$base] = $file;
    }
}

$collisions = [];
foreach ($addedFiles as $added) {
    $addedBase = basename($added, '.php');
    if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_(.+)$/', $addedBase, $m)) {
        $suffix = $m[1];
    } else {
        $suffix = $addedBase;
    }

    if (isset($existingSuffixes[$suffix])) {
        $collisions[$suffix] = ['existing' => $existingSuffixes[$suffix], 'added' => $added];
    }
}

// Write deterministic JSON output for CI to assert against
$outputPath = __DIR__.'/check-pr-migration-suffixes.harness.output.json';
file_put_contents($outputPath, json_encode(['collisions' => $collisions], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

if (! empty($collisions)) {
    fwrite(STDERR, "Collisions detected, output written to $outputPath\n");
    exit(1);
}

fwrite(STDOUT, "No collisions, output written to $outputPath\n");
exit(0);
