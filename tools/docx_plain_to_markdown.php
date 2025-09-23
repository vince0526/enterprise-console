<?php

declare(strict_types=1);

/**
 * Alternative DOCX plain text -> Markdown converter.
 * Emphasises a stricter H1 detection and bullet normalisation.
 */
$input = 'full_doc_plain.txt';
$output = 'docs/enterprise_management_console.md';
if (! is_file($input)) {
    fwrite(STDERR, "Missing $input. Run extraction first.\n");
    exit(1);
}

$lines = preg_split('/\r?\n/', (string) file_get_contents($input));
$md = [];
$titleDone = false;
$flushBlank = false;

foreach ($lines as $raw) {
    $line = trim($raw);
    if ($line === '' || preg_match('/^w14:paraId=/', $line)) {
        if (! $flushBlank) {
            $md[] = '';
            $flushBlank = true;
        }

        continue;
    }
    $flushBlank = false;

    $line = preg_replace('/`n`n/', ' ', $line);

    if (! $titleDone && stripos($line, 'Enterprise Database Management System') !== false) {
        $md[] = '# Enterprise Database Management System';
        $titleDone = true;

        continue;
    }

    if (preg_match('/^[A-Z][A-Z \-&]{2,80}$/', $line) && ! preg_match('/\d/', $line)) {
        $md[] = '## '.ucwords(strtolower($line));

        continue;
    }

    if (preg_match('/^(\d+)\.\s+(.*)$/', $line, $m)) {
        $md[] = $m[1].'. '.$m[2];

        continue;
    }

    if (preg_match('/^[\-*•]\s+(.*)$/', $line, $m)) {
        $md[] = '- '.$m[1];

        continue;
    }

    if (preg_match('/^([A-Z][A-Z0-9_]+):\s+(.*)$/', $line, $m)) {
        $md[] = '**'.$m[1].':** '.$m[2];

        continue;
    }

    $md[] = $line;
}

$render = preg_replace("/\n{3,}/", "\n\n", implode("\n", $md));
if (! is_dir(dirname($output))) {
    mkdir(dirname($output), 0777, true);
}
file_put_contents($output, $render);
echo 'Generated '.$output.' ('.strlen($render).' bytes)'."\n";
