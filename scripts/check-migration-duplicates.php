<?php

declare(strict_types=1);

// Simple guard to detect duplicate migration "suffixes" (the part after the timestamp).
// This prevents accidental duplicate migration files like
// 2025_09_07_114318_create_database_connections_table.php and
// 2025_09_07_144752_create_database_connections_table.php from both existing.

$migrationsDir = __DIR__.'/../database/migrations';
$files = glob($migrationsDir.'/*.php') ?: [];

$suffixMap = [];
foreach ($files as $file) {
    $base = basename($file, '.php');

    if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_(.+)$/', $base, $m)) {
        $suffix = $m[1];
    } else {
        // If filename doesn't start with timestamp, use the whole name as suffix.
        $suffix = $base;
    }

    $suffixMap[$suffix][] = $file;
}

$duplicates = array_filter($suffixMap, fn ($paths) => count($paths) > 1);

if (! empty($duplicates)) {
    fwrite(STDERR, "Duplicate migration suffixes detected:\n");
    foreach ($duplicates as $suffix => $paths) {
        fwrite(STDERR, "- $suffix\n");
        foreach ($paths as $p) {
            fwrite(STDERR, "    * $p\n");
        }
    }

    exit(1);
}

fwrite(STDOUT, "No duplicate migration suffixes found.\n");
exit(0);
