<?php

declare(strict_types=1);

/**
 * Generate structured markdown from full_doc_plain.txt (extracted DOCX text).
 *
 * Heuristics:
 *  - ALL CAPS short lines (<80 chars) => level 2 heading
 *  - Lines ending with keywords (Management|System|List|Log|Workspace) => level 3 heading
 *  - Numbered lines (e.g. 1.) retained as ordered list items
 *  - Remove artifact lines beginning with w14:
 *  - Deduplicate consecutive identical lines
 */
$input = realpath(__DIR__.'/../full_doc_plain.txt');
if ($input === false) {
    fwrite(STDERR, "Input full_doc_plain.txt not found\n");
    exit(1);
}
$output = __DIR__.'/../docs/enterprise_management_console.md';

$raw = file($input, FILE_IGNORE_NEW_LINES);
$lines = [];
foreach ($raw as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, 'w14:')) {
        continue;
    }
    $line = str_replace('`n', ' ', $line);
    $lines[] = $line;
}

$md = [
    '# Enterprise Management Console',
    '',
    '> Auto-generated from DOCX source. Manual curation recommended.',
    '',
];
$seen = [];

$isAllCaps = static function (string $s): bool {
    if ($s === '' || strlen($s) > 80) {
        return false;
    }
    $letters = preg_replace('/[^A-Za-z]/', '', $s);
    if ($letters === '') {
        return false;
    }

    return strtoupper($letters) === $letters && preg_match('/[A-Z]/', $letters) === 1;
};

$isLevel3 = static function (string $s): bool {
    return (bool) preg_match('/(Management|System|List|Log|Workspace)$/', $s);
};

foreach ($lines as $line) {
    $last = end($md);
    if ($last === $line) {
        continue; // consecutive duplicate
    }
    if ($isAllCaps($line)) {
        $h = '## '.ucwords(strtolower($line));
        if (! isset($seen[$h])) {
            $md[] = $h;
            $md[] = '';
            $seen[$h] = true;
        }

        continue;
    }
    if ($isLevel3($line)) {
        $h3 = '### '.$line;
        if (! isset($seen[$h3])) {
            $md[] = $h3;
            $md[] = '';
            $seen[$h3] = true;
        }

        continue;
    }
    if (preg_match('/^\d+\./', $line)) {
        $md[] = $line; // preserve numbering

        continue;
    }
    $md[] = $line;
}

file_put_contents($output, implode("\n", $md)."\n");
fwrite(STDOUT, "Generated markdown: $output\n");
