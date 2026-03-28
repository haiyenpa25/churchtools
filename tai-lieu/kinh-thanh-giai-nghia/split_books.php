<?php

$dir = __DIR__;
$files = ['Giai-nghia-kt1.txt', 'Giai-nghia-kt2.txt', 'Giai-nghia-kt3.txt'];
$outDir = $dir . '/books';

if (!is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

// Hàm bỏ dấu tiếng Việt để tạo tên file đẹp
function removeAccents($str) {
    $unicode = array(
        'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd'=>'đ',
        'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i'=>'í|ì|ỉ|ĩ|ị',
        'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
        'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D'=>'Đ',
        'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
        'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );
    foreach($unicode as $nonUnicode=>$uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    // Repalce spaces and special chars with dash
    $str = str_replace(' ', '-', trim($str));
    $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str);
    $str = preg_replace('/-+/', '-', $str);
    return strtolower($str);
}

$bookCounter = 1;

foreach ($files as $file) {
    $filePath = $dir . '/' . $file;
    if (!file_exists($filePath)) {
        echo "File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    // Chuẩn hóa line endings
    $content = str_replace("\r\n", "\n", $content);
    
    // Tách các sách bằng vạch ngang
    $chunks = explode("--------------------", $content);
    
    foreach ($chunks as $chunk) {
        $chunk = trim($chunk);
        if (empty($chunk)) continue;
        
        $lines = explode("\n", $chunk);
        $bookName = "";
        
        // Tìm dòng đầu tiên không rỗng làm tên sách
        foreach ($lines as $line) {
            $line = trim($line);
            // Bỏ qua dòng "Không có tiêu đề"
            if (!empty($line) && strpos(mb_strtolower($line, 'UTF-8'), 'không có tiêu đề') === false) {
                $bookName = $line;
                break;
            }
        }
        
        if (empty($bookName)) continue;
        
        $safeName = removeAccents($bookName);
        $prefix = str_pad($bookCounter, 2, "0", STR_PAD_LEFT);
        $fileName = "{$prefix}_{$safeName}.txt";
        
        // Gắn lại dòng gạch ngang phía trên cho đúng nguyên gốc
        $finalContent = "--------------------\n" . ltrim($chunk);
        
        file_put_contents($outDir . '/' . $fileName, $finalContent);
        echo "Tách thành công: $fileName\n";
        
        $bookCounter++;
    }
}
echo "Hoàn thành tách " . ($bookCounter - 1) . " sách!\n";

?>
