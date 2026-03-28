<?php
$booksDir = __DIR__ . '/books';
$parsedDir = __DIR__ . '/parsed';

if (!is_dir($parsedDir)) {
    mkdir($parsedDir, 0777, true);
}

$files = scandir($booksDir);
$count = 0;

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'txt') continue;

    $bookFile = $booksDir . '/' . $file;
    $jsonFile = $parsedDir . '/' . str_replace('.txt', '.json', $file);

    $content = file_get_contents($bookFile);
    $content = str_replace("\r\n", "\n", $content);
    $lines = explode("\n", $content);

    $result = [
        'book_name' => '',
        'author' => '',
        'intro' => '',
        'chapters' => []
    ];

    $state = 'HEADER';
    $currentChapterTitle = '';
    $currentChapterRange = '';
    $currentChapterContent = [];
    $currentVerseRefs = '';
    $currentVerseContent = '';
    $introText = "";

    foreach ($lines as $line) {
        if (trim($line) === '') continue;
        
        if ($state === 'HEADER') {
            if (strpos($line, '---') !== false) continue;
            if ($result['book_name'] === '') {
                // assume first non-empty line is book name
                $result['book_name'] = trim($line);
                continue;
            }
            if (strpos($line, 'Được viết bởi:') !== false || strpos($line, 'Nguyễn Thiên Ý') !== false || strpos($line, 'Chuyên mục:') !== false || strpos($line, 'Warren W. Wiersbe') !== false) {
                continue; 
            }
            
            if (preg_match('/^(\d+)\.\s+(.*?)\s*\(([A-Za-z]+\s\d+:\d+.*?)\)/i', $line, $matches) || preg_match('/^Giới thiệu:\s*(.*)/i', $line, $matchesIntro)) {
                $state = 'CHAPTER';
            } else {
                if ($result['book_name'] !== '') {
                    $introText .= $line . "\n";
                }
                continue;
            }
        }
        
        if ($state === 'CHAPTER' || $state === 'VERSE') {
            if (preg_match('/^(\d+)\.\s+(.*?)\s*\(([A-Za-z]+\s\d+:\d+.*?)\)/i', $line, $matches)) {
                if ($currentVerseContent !== '') {
                    $currentChapterContent[] = [
                        'reference' => $currentVerseRefs,
                        'content' => trim($currentVerseContent)
                    ];
                    $currentVerseRefs = '';
                    $currentVerseContent = '';
                }
                if ($currentChapterTitle !== '') {
                    $result['chapters'][] = [
                        'title' => $currentChapterTitle,
                        'reference' => $currentChapterRange,
                        'verses' => $currentChapterContent
                    ];
                }
                
                $currentChapterTitle = trim($matches[1] . ". " . $matches[2]);
                $currentChapterRange = trim($matches[3]);
                $currentChapterContent = [];
                $state = 'VERSE';
                continue;
            }
            
            if (preg_match('/^(?:\d+\.\s+.*?|\s*)\(?([A-Za-z]+(?:\s\d+[a-z]?)?\s\d+:\d+(?:-\d+)?)\)?\.?\s*(.*)/i', $line, $matchesVerse)) {
                if ($currentVerseContent !== '') {
                    $currentChapterContent[] = [
                        'reference' => $currentVerseRefs,
                        'content' => trim($currentVerseContent)
                    ];
                }
                $currentVerseRefs = ltrim($matchesVerse[1], '(');
                $currentVerseRefs = rtrim($currentVerseRefs, ')');
                
                $currentVerseContent = ltrim($matchesVerse[2], '. ');
                $state = 'VERSE';
                continue;
            }
            
            if ($state === 'VERSE') {
                $currentVerseContent .= "\n" . trim($line);
            } else if ($state === 'CHAPTER' && $currentChapterTitle !== '') {
                $currentVerseContent .= "\n" . trim($line); 
            }
        }
    }

    if ($currentVerseContent !== '') {
        $currentChapterContent[] = [
            'reference' => $currentVerseRefs,
            'content' => trim($currentVerseContent)
        ];
    }
    if ($currentChapterTitle !== '') {
        $result['chapters'][] = [
            'title' => $currentChapterTitle,
            'reference' => $currentChapterRange,
            'verses' => $currentChapterContent
        ];
    }

    $result['intro'] = trim($introText);

    $encoded = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($jsonFile, $encoded);
    echo "Parsed " . $file . " -> " . count($result['chapters']) . " sections\n";
    $count++;
}

echo "\nTotal parsed: $count books.\n";
?>
