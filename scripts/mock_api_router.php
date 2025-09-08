<?php

declare(strict_types=1);
// Simple mock router for GitHub PR files API used in local testing.
// Usage (from repo root): php -S localhost:8001 -t scripts scripts/mock_api_router.php

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Example path: /repos/vince0526/enterprise-console/pulls/123/files
if (preg_match('#^/repos/([^/]+)/([^/]+)/pulls/(\d+)/files#', $uri, $m)) {
    header('Content-Type: application/json');

    // Return a sample list of files where one added migration collides with existing suffix 'create_users_table'
    $files = [
        ['filename' => 'app/Models/User.php', 'status' => 'modified'],
        ['filename' => 'database/migrations/2025_09_09_000001_create_users_table.php', 'status' => 'added'],
        ['filename' => 'database/migrations/2025_09_09_000002_create_posts_table.php', 'status' => 'added'],
    ];

    echo json_encode($files);
    exit;
}

// Default 404
http_response_code(404);
echo json_encode(['message' => 'not found']);
