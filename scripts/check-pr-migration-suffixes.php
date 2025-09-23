<?php

declare(strict_types=1);

// API-based PR migration suffix checker
// - Uses GITHUB_EVENT_PATH to obtain PR number
// - Uses the REST API to list PR files (robust across checkout strategies)
// - Supports MIGRATION_SUFFIX_WHITELIST env var (comma separated suffixes) to allow collisions
// - Posts a comment to the PR with details when collisions are found (requires GITHUB_TOKEN)

// Helpers
function gh_api_get(string $url, ?string $token): array
{
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: php-script',
                'Accept: application/vnd.github.v3+json',
            ],
            'ignore_errors' => true,
        ],
    ];
    if ($token) {
        $opts['http']['header'][] = 'Authorization: token '.$token;
    }
    $ctx = stream_context_create($opts);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        return [];
    }
    $data = json_decode($raw, true);

    return is_array($data) ? $data : [];
}

function gh_api_post(string $url, array $body, ?string $token): array
{
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => [
                'User-Agent: php-script',
                'Accept: application/vnd.github.v3+json',
                'Content-Type: application/json',
            ],
            'content' => json_encode($body),
            'ignore_errors' => true,
        ],
    ];
    if ($token) {
        $opts['http']['header'][] = 'Authorization: token '.$token;
    }
    $ctx = stream_context_create($opts);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        return [];
    }
    $data = json_decode($raw, true);

    return is_array($data) ? $data : [];
}

$eventPath = getenv('GITHUB_EVENT_PATH') ?: ($argv[1] ?? null);
if (! $eventPath || ! file_exists($eventPath)) {
    fwrite(STDERR, "GITHUB_EVENT_PATH not set or file not found. This script must run in a GitHub Actions PR job or be provided with the event path as an argument.\n");
    exit(2);
}

$payload = json_decode(file_get_contents($eventPath), true);
if (! is_array($payload)) {
    fwrite(STDERR, "Invalid JSON payload at GITHUB_EVENT_PATH.\n");
    exit(2);
}

$repo = getenv('GITHUB_REPOSITORY') ?: ($payload['repository']['full_name'] ?? null);
if (! $repo || ! str_contains($repo, '/')) {
    fwrite(STDERR, "GITHUB_REPOSITORY not available or invalid.\n");
    exit(2);
}

[$owner, $repoName] = explode('/', $repo, 2);

$prNumber = $payload['pull_request']['number'] ?? null;
if (! $prNumber) {
    fwrite(STDERR, "Cannot determine pull request number from event payload.\n");
    exit(2);
}

$token = getenv('GITHUB_TOKEN') ?: null;
$whitelistRaw = getenv('MIGRATION_SUFFIX_WHITELIST') ?: '';
$whitelist = array_filter(array_map('trim', explode(',', $whitelistRaw)));

$addedFiles = [];

$apiBase = getenv('GITHUB_API_BASE_URL') ?: 'https://api.github.com';

// Paginate through PR files via API
$page = 1;
do {
    $url = sprintf('%s/repos/%s/%s/pulls/%d/files?page=%d&per_page=100', rtrim($apiBase, '/'), rawurlencode($owner), rawurlencode($repoName), $prNumber, $page);
    $files = gh_api_get($url, $token);
    if (! is_array($files) || empty($files)) {
        break;
    }
    foreach ($files as $f) {
        // file object has filename and status
        if (! empty($f['filename']) && ($f['status'] ?? '') === 'added' && str_starts_with($f['filename'], 'database/migrations/')) {
            $addedFiles[] = $f['filename'];
        }
    }
    $page++;
} while (count($files) === 100);

if (empty($addedFiles)) {
    fwrite(STDOUT, "No added migration files detected in this PR.\n");
    exit(0);
}

$migrationsDir = __DIR__.'/../database/migrations';
$existing = glob($migrationsDir.'/*.php') ?: [];

// Map existing suffixes
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

    if (in_array($suffix, $whitelist, true)) {
        // allowed collision
        continue;
    }

    if (isset($existingSuffixes[$suffix])) {
        $collisions[$suffix] = ['existing' => $existingSuffixes[$suffix], 'added' => $added];
    }
}

if (! empty($collisions)) {
    // Compose a helpful comment
    $lines = [
        '**Migration suffix collisions detected**',
        'The PR adds migration files whose suffix (filename after timestamp) already exists in this repository. This can cause duplicate table creation during migrations and should be resolved before merging.',
        '',
        'Collisions:',
    ];
    foreach ($collisions as $suf => $pair) {
        $lines[] = "- **$suf**";
        $lines[] = "  - existing: `{$pair['existing']}`";
        $lines[] = "  - added: `{$pair['added']}`";
    }
    $lines[] = '';
    $lines[] = 'Suggested fixes:';
    $lines[] = '- Remove the added migration and run `php artisan vendor:publish` or create a single canonical migration.';
    $lines[] = "- Rename the added migration to use a different suffix (not recommended unless you know what you're doing).";

    $commentBody = implode("\n", $lines);

    // Post a comment to the PR if we have a token
    if ($token) {
        $commentUrl = sprintf('https://api.github.com/repos/%s/%s/issues/%d/comments', rawurlencode($owner), rawurlencode($repoName), $prNumber);
        gh_api_post($commentUrl, ['body' => $commentBody], $token);
    }

    // Also print the collisions to STDOUT so they appear in Action logs for easier debugging
    fwrite(STDOUT, "PR migration suffix collisions detected:\n");
    fwrite(STDOUT, $commentBody."\n");
    // Output a machine-readable JSON blob as well
    fwrite(STDOUT, 'COLLISIONS_JSON:'.json_encode($collisions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n");

    // Log short note to STDERR for backward compatibility with existing checks
    fwrite(STDERR, "PR migration suffix collisions detected. See PR comment for details.\n");
    exit(1);
}

fwrite(STDOUT, "No PR migration suffix collisions detected.\n");
exit(0);
