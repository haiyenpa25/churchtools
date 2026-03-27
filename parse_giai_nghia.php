<?php

mb_internal_encoding('UTF-8');

$bookTitles = [
    'SÁNG THẾ KÝ' => ['01', 'Sang-the-ky'],
    'XUẤT Ê-DÍP-TÔ KÝ' => ['02', 'Xuat-e-dip-to-ky'],
    'LÊ-VI KÝ' => ['03', 'Le-vy-ky'],
    'LÊ-VY KÝ' => ['03', 'Le-vy-ky'],
    'DÂN SỐ KÝ' => ['04', 'Dan-so-ky'],
    'PHỤC TRUYỀN LUẬT LỆ KÝ' => ['05', 'Phuc-truyen-luat-le-ky'],
    'GIÔ-SUÊ' => ['06', 'Gio-sue'],
    'CÁC QUAN XÉT' => ['07', 'Cac-quan-xet'],
    'RU-TƠ' => ['08', 'Ru-to'],
    'I SA-MU-ÊN' => ['09', 'I Sa-mu-en'],
    'II SA-MU-ÊN' => ['10', 'II Sa-mu-en'],
    'I CÁC VUA' => ['11', 'I Cac-vua'],
    'II CÁC VUA' => ['12', 'II Cac-vua'],
    'I SỬ KÝ' => ['13', 'I Su-ky'],
    'II SỬ KÝ' => ['14', 'II Su-ky'],
    'Ê-XƠ-RA' => ['15', 'E-xo-ra'],
    'NÊ-HÊ-MI' => ['16', 'Ne-he-mi'],
    'Ê-XƠ-TÊ' => ['17', 'E-xo-te'],
    'GIÓP' => ['18', 'Giop'],
    'THI THIÊN' => ['19', 'Thi-thien'],
    'CHÂM NGÔN' => ['20', 'Cham-ngon'],
    'TRUYỀN ĐẠO' => ['21', 'Truyen-dao'],
    'NHÃ CA' => ['22', 'Nha-ca'],
    'Ê-SAI' => ['23', 'E-sai'],
    'GIÊ-RÊ-MI' => ['24', 'Gie-re-mi'],
    'CA THƯƠNG' => ['25', 'Ca-thuong'],
    'Ê-XÊ-CHI-ÊN' => ['26', 'E-xe-chi-en'],
    'ĐA-NI-ÊN' => ['27', 'Da-ni-en'],
    'Ô-SÊ' => ['28', 'O-se'],
    'GIÔ-ÊN' => ['29', 'Gio-en'],
    'A-MỐT' => ['30', 'A-mot'],
    'ÁP-ĐIA' => ['31', 'Ap-dia'],
    'GIÔ-NA' => ['32', 'Gio-na'],
    'MI-CHÊ' => ['33', 'Mi-che'],
    'NA-HUM' => ['34', 'Na-hum'],
    'HA-BA-CÚC' => ['35', 'Ha-ba-cuc'],
    'SÔ-PHÔ-NI' => ['36', 'So-pho-ni'],
    'A-GHÊ' => ['37', 'A-ghe'],
    'XA-CHA-RI' => ['38', 'Xa-cha-ri'],
    'MA-LA-CHI' => ['39', 'Ma-la-chi'],
    'MA-THI-Ơ' => ['40', 'Ma-thi-o'],
    'MÁC' => ['41', 'Mac'],
    'LU-CA' => ['42', 'Lu-ca'],
    'GIĂNG' => ['43', 'Giang'],
    'CÔNG VỤ CÁC SỨ ĐỒ' => ['44', 'Cong-vu-cac-su-do'],
    'CÔNG VỤ' => ['44', 'Cong-vu-cac-su-do'],
    'RÔ-MA' => ['45', 'Ro-ma'],
    'I CÔ-RINH-TÔ' => ['46', 'I Co-rinh-to'],
    'II CÔ-RINH-TÔ' => ['47', 'II Co-rinh-to'],
    'GA-LA-TI' => ['48', 'Ga-la-ti'],
    'Ê-PHÊ-SÔ' => ['49', 'E-phe-so'],
    'PHI-LÍP' => ['50', 'Phi-lip'],
    'CÔ-LÔ-SÊ' => ['51', 'Co-lo-se'],
    'I TÊ-SA-LÔ-NI-CA' => ['52', 'I Te-sa-lo-ni-ca'],
    'II TÊ-SA-LÔ-NI-CA' => ['53', 'II Te-sa-lo-ni-ca'],
    'I TI-MÔ-THÊ' => ['54', 'I Ti-mo-the'],
    'II TI-MÔ-THÊ' => ['55', 'II Ti-mo-the'],
    'TÍT' => ['56', 'Tit'],
    'PHI-LÊ-MÔN' => ['57', 'Phi-le-mon'],
    'HÊ-BƠ-RƠ' => ['58', 'He-bo-ro'],
    'GIA-CƠ' => ['59', 'Gia-co'],
    'I PHI-Ê-RƠ' => ['60', 'I Phi-e-ro'],
    'II PHI-Ê-RƠ' => ['61', 'II Phi-e-ro'],
    'I GIĂNG' => ['62', 'I Giang'],
    'II GIĂNG' => ['63', 'II Giang'],
    'III GIĂNG' => ['64', 'III Giang'],
    'GIU-ĐÊ' => ['65', 'Giu-de'],
    'KHẢI HUYỀN' => ['66', 'Khai-huyen'],
];

$outputDir = __DIR__.'/tai-lieu/kinh-thanh-giai-nghia';
if (! is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Xóa file rác từ lần chạy lỗi trước
array_map('unlink', glob($outputDir.'/*_Giai_Nghia.txt'));
array_map('unlink', glob($outputDir.'/[0-9]*_*.txt'));

$filesToProcess = [
    $outputDir.'/Giai-nghia-kt1.txt',
    $outputDir.'/Giai-nghia-kt2.txt',
    $outputDir.'/Giai-nghia-kt3.txt',
];

$currentBookPrefix = null;
$currentBookSlug = null;
$currentBookNameStr = null;
$currentChapter = null;
$buffer = [];
$totalFilesCreated = 0;

function flushBuffer()
{
    global $currentBookPrefix, $currentBookSlug, $currentChapter, $buffer, $outputDir, $totalFilesCreated;

    if ($currentBookPrefix && $currentBookSlug && count($buffer) > 0) {
        $chapter = $currentChapter ? $currentChapter : '1';
        // Đặt tên file giống thư mục Kinh Thánh
        $fileName = $currentBookPrefix.'_'.$currentBookSlug.'_'.$chapter.'.txt';
        $filePath = $outputDir.'/'.$fileName;

        file_put_contents($filePath, implode("\n", $buffer)."\n", FILE_APPEND);
        $totalFilesCreated++;
    }
    $buffer = [];
}

foreach ($filesToProcess as $file) {
    if (! file_exists($file)) {
        continue;
    }

    echo "Processing $file...\n";
    $content = file_get_contents($file);
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
    $lines = explode("\n", str_replace("\r", '', $content));

    foreach ($lines as $line) {
        $trimLine = trim($line);
        if (empty($trimLine)) {
            $buffer[] = $line;

            continue;
        }

        $upperLine = mb_strtoupper($trimLine, 'UTF-8');

        // Cấp 1: Kiểm tra Header đổi Sách
        if (isset($bookTitles[$upperLine])) {
            flushBuffer();
            $currentBookPrefix = $bookTitles[$upperLine][0];
            $currentBookSlug = $bookTitles[$upperLine][1];
            $currentBookNameStr = $upperLine; // Lưu tên Sách gốc để so ký tự
            $currentChapter = null;
            echo "-> Chuyển sang Sách: $upperLine ($currentBookPrefix)\n";
            $buffer[] = $line;

            continue;
        }

        // Cấp 2: Kiểm tra Headings đổi Chương (Tránh Cross-References rác)
        if ($currentBookPrefix && mb_strlen($trimLine, 'UTF-8') < 200) {

            // Tìm kiếm Parentheses kiểu (Sa 1:1)
            if (preg_match('/\(([A-ZĐa-zđỹ\s]+?)\s+(\d+)\s*[:\-]/u', $trimLine, $matches)) {
                $abbrev = trim($matches[1]);
                $chapterNum = $matches[2];

                // Heuristic: Lấy ký tự đầu tiên của Abbrev và BookName để so sánh
                $abbrevFirstChar = mb_strtoupper(mb_substr($abbrev, 0, 1, 'UTF-8'), 'UTF-8');
                $bookFirstChar = mb_strtoupper(mb_substr($currentBookNameStr, 0, 1, 'UTF-8'), 'UTF-8');

                // Nếu khớp chữ cái đầu, khả năng 99% đây là heading của sách hiện tại.
                if ($abbrevFirstChar === $bookFirstChar) {
                    // Nếu đây là chương mới, flush chương cũ
                    if ($chapterNum !== $currentChapter && $currentChapter !== null) {
                        flushBuffer();
                        $currentChapter = $chapterNum;
                    } elseif ($currentChapter === null) {
                        $currentChapter = $chapterNum;
                    }
                }
            }
        }

        $buffer[] = $line;
    }
}

flushBuffer();

echo "Hoàn thành! Đã tạo/rải dữ liệu vào $totalFilesCreated lần file chapter.\n";
