<?php

declare(strict_types=1);

// Local variant of the PR migration checker that points to a mock API base URL for testing.
require __DIR__.'/check-pr-migration-suffixes.php';
// The original script reads its $eventPath from argv or GITHUB_EVENT_PATH and calls gh_api_get with full URLs.
// For local testing we'll set GITHUB_EVENT_PATH to the rich test event and override the API base by defining a constant.

// Not much to change here because the main script uses full URLs; we'll instead set GITHUB_API_BASE_URL env var expected by the main script (we need to modify main script to use it).
