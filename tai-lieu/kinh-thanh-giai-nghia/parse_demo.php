<?php
$dir = __DIR__;
$bookFile = $dir . '/books/01_sang-the-ky.txt';
$jsonFile = $dir . '/01_sang-the-ky_parsed.json';

if (!file_exists($bookFile)) {
    die("Khong tim thay file $bookFile\n");
}

$content = file_get_contents($bookFile);
$content = str_replace("\r\n", "\n", $content);
$lines = explode("\n", $content);

$result = [
    'book_name' => '',
    'author' => '',
    'intro' => '',
    'chapters' => []
];

// Parser State
$state = 'HEADER';
$currentChapterTitle = '';
$currentChapterRange = '';
$currentChapterContent = [];
$currentVerseRefs = '';
$currentVerseContent = '';
$introText = "";

foreach ($lines as $line) {
    if (trim($line) === '') continue;
    
    // --- Bắt thông tin Header ---
    if ($state === 'HEADER') {
        if (strpos($line, '---') !== false) continue;
        if (trim($line) === 'SÁNG THẾ KÝ' || trim($line) === 'SÁNG-THẾ KÝ') {
            $result['book_name'] = trim($line);
            continue;
        }
        if (strpos($line, 'Được viết bởi:') !== false || strpos($line, 'Nguyễn Thiên Ý') !== false || strpos($line, 'Chuyên mục:') !== false || strpos($line, 'Warren W. Wiersbe') !== false) {
            continue; // Bỏ qua Header metadata
        }
        
        // Phát hiện Chương/Phân đoạn lớn bắt đầu bằng [Số]. 
        // Vd: "2. KHI ĐỨC CHÚA TRỜI PHÁN, ĐIỀU GÌ ĐÓ XẢY RA (Sa 1:1-31)"
        // Cập nhật Regex: Chấp nhận mọi ký tự cho phần Tiêu đề
        if (preg_match('/^(\d+)\.\s+(.*?)\s*\(([A-Za-z]+\s\d+:\d+.*?)\)/i', $line, $matches) || preg_match('/^Giới thiệu:\s*(.*)/i', $line, $matchesIntro)) {
            $state = 'CHAPTER';
            // Không break, chuyển qua check ở case CHAPTER
        } else {
            if ($result['book_name'] !== '') {
                $introText .= $line . "\n";
            }
            continue;
        }
    }
    
    // --- Bắt thông tin Chương lớn ---
    if ($state === 'CHAPTER' || $state === 'VERSE') {
        // Dấu hiệu Chương mới: 2. CHÚA TẠO DỰNG (Sa 1:1)
        if (preg_match('/^(\d+)\.\s+(.*?)\s*\(([A-Za-z]+\s\d+:\d+.*?)\)/i', $line, $matches)) {
            // Lưu verse cũ nếu có
            if ($currentVerseContent !== '') {
                $currentChapterContent[] = [
                    'reference' => $currentVerseRefs,
                    'content' => trim($currentVerseContent)
                ];
                $currentVerseRefs = '';
                $currentVerseContent = '';
            }
            // Lưu chapter cũ nếu có
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
        
        // Dấu hiệu khởi đầu một đoạn giải nghĩa câu: "(Sa 1:3-5). Nội dung..." hoặc "1. Ngày thứ nhất (Sa 1:3-5)"
        // Mẫu 1: X. Tiêu đề (Sách x:y)
        // Mẫu 2: (Sách x:y). Text...
        if (preg_match('/^(?:\d+\.\s+.*?|\s*)\(?([A-Za-z]+(?:\s\d+[a-z]?)?\s\d+:\d+(?:-\d+)?)\)?\.?\s*(.*)/i', $line, $matchesVerse)) {
            // Kiểm tra xem dòng này có phải là một Chương Lớn bị catch nhầm không? (Nếu nó rớt xuống đây tức là regex trên không match)
            // Lưu cụm cũ lại
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
        
        // Nếu không khớp pattern nào, gom vào Verse content hiện tại
        if ($state === 'VERSE') {
            $currentVerseContent .= "\n" . trim($line);
        } else if ($state === 'CHAPTER' && $currentChapterTitle !== '') {
            $currentVerseContent .= "\n" . trim($line); // Dù không có refs cũng gom chung
        }
    }
}

// Lưu cái cuối cùng
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

// Trả về JSON
$encoded = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents($jsonFile, $encoded);
echo "Da parse thanh cong " . count($result['chapters']) . " Chuong lon trong Sang-the-ky!\n";
echo "Luu ra $jsonFile\n";
?>
