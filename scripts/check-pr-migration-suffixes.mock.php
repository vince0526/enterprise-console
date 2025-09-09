<?php

declare(strict_types=1);

// Mock runner: loads the real event payload and uses a hard-coded files list instead of HTTP API calls.
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

$repo = $payload['repository']['full_name'] ?? null;
[$owner, $repoName] = explode('/', $repo, 2);
$prNumber = $payload['pull_request']['number'] ?? null;

// Hard-coded PR files list (simulate added migrations)
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

if (! empty($collisions)) {
    echo "Collisions detected:\n";
    foreach ($collisions as $suf => $pair) {
        echo "- $suf\n";
        echo "  existing: {$pair['existing']}\n";
        echo "  added: {$pair['added']}\n";
    }
    exit(1);
}

echo "No collisions detected\n";
exit(0);
