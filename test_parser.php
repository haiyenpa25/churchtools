<?php

mb_internal_encoding('UTF-8');

$file = __DIR__.'/tai-lieu/kinh-thanh-giai-nghia/Giai-nghia-kt1.txt';
if (! file_exists($file)) {
    exit('File not found');
}

$content = file_get_contents($file);
$lines = explode("\n", $content);

$bookMap = [
    'SÁNG THẾ KÝ' => '01_Sang-the-ky',
    'XUẤT Ê-DÍP-TÔ KÝ' => '02_Xuat-e-dip-to-ky',
    // We will expand this later...
];

$currentBook = null;
$matches = [];

foreach ($lines as $i => $line) {
    $line = trim($line);
    if (empty($line)) {
        continue;
    }

    if (isset($bookMap[$line])) {
        echo "Found Book: $line\n";
        $currentBook = $bookMap[$line];
    }

    // Look for patterns like: 2. KHI ĐỨC CHÚA TRỜI PHÁN... (Sa 1:1-31)
    // Or: Giới thiệu: TRƯỚC CÔNG NGUYÊN: TRƯỚC SỰ SÁNG TẠO (Sáng 1-50:26)
    if (preg_match('/\(.*? (\d+)[-:]/', $line, $m)) {
        // Just print lines containing parentheses with a number, to see how headings look
        if (preg_match('/^[A-Z0-9]/', $line) && strlen($line) < 150) {
            echo "Line $i: $line\n";
        }
    }
}
