<?php

declare(strict_types=1);

/**
 * Quick DOCX to plain text extractor (best effort).
 * Reads word/document.xml inside the DOCX zip and emits a simplified text version.
 *
 * Usage:
 *  php tools/extract_docx_text.php path/to/file.docx [--no-truncate]
 */
if ($argc < 2) {
    fwrite(STDERR, 'Usage: php tools/extract_docx_text.php <docx-path> [--no-truncate]'."\n");
    exit(1);
}

$path = $argv[1];
$noTruncate = in_array('--no-truncate', $argv, true);

if (! is_file($path)) {
    fwrite(STDERR, 'File not found: '.$path."\n");
    exit(1);
}

$zip = new ZipArchive;
if ($zip->open($path) !== true) {
    fwrite(STDERR, 'Could not open DOCX: '.$path."\n");
    exit(1);
}
$xml = $zip->getFromName('word/document.xml');
$zip->close();
if ($xml === false) {
    fwrite(STDERR, "document.xml not found in archive\n");
    exit(1);
}

// Normalize paragraphs and line breaks to newlines.
$xml = preg_replace('/<w:p[\s>]/i', "\n<w:p ", $xml);
$xml = preg_replace('/<w:br[^>]*>/i', "\n", $xml);

// Strip remaining tags and decode entities.
$text = strip_tags($xml);
$text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Collapse excessive blank lines.
$text = preg_replace("/\n{3,}/", "\n\n", $text);
$text = trim($text);

if ($noTruncate) {
    echo $text, "\n";
    exit(0);
}

// Truncate for safety when dumping to console.
$max = 4000;
if (strlen($text) > $max) {
    $snippet = substr($text, 0, $max);
    echo $snippet, "\n\n--- TRUNCATED (".(strlen($text) - $max).' more chars) ---'."\n";
    exit(0);
}

echo $text, "\n";
