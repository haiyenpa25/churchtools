<?php

$books = [ // Cựu Ước (Old Testament) - 39 Books    'Sang-the-ky' => '01',    'Xuat-e-dip-to-ky' => '02',    'Le-vy-ky' => '03',    'Dan-so-ky' => '04',    'Phuc-truyen-luat-le-ky' => '05',    'Gio-sue' => '06',    'Cac-quan-xet' => '07',    'Ru-to' => '08',    'I Sa-mu-en' => '09',    'II Sa-mu-en' => '10',    'I Cac-vua' => '11',    'II Cac-vua' => '12',    'I Su-ky' => '13',    'II Su-ky' => '14',    'E-xo-ra' => '15',    'Ne-he-mi' => '16',    'E-xo-te' => '17',    'Giop' => '18',    'Thi-thien' => '19',    'Cham-ngon' => '20',    'Truyen-dao' => '21',    'Nha-ca' => '22',    'E-sai' => '23',    'Gie-re-mi' => '24',    'Ca-thuong' => '25',    'E-xe-chi-en' => '26',    'Da-ni-en' => '27',    'O-se' => '28',    'Gio-en' => '29',    'A-mot' => '30',    'Ap-dia' => '31',    'Gio-na' => '32',    'Mi-che' => '33',    'Na-hum' => '34',    'Ha-ba-cuc' => '35',    'So-pho-ni' => '36',    'A-ghe' => '37',    'Xa-cha-ri' => '38',    'Ma-la-chi' => '39',
    // Tân Ước (New Testament) - 27 Books    'Ma-thi-o' => '40',    'Mac' => '41',    'Lu-ca' => '42',    'Giang' => '43',    'Cong-vu-cac-su-do' => '44',    'Ro-ma' => '45',    'I Co-rinh-to' => '46',    'II Co-rinh-to' => '47',    'Ga-la-ti' => '48',    'E-phe-so' => '49',    'Phi-lip' => '50',    'Co-lo-se' => '51',    'I Te-sa-lo-ni-ca' => '52',    'II Te-sa-lo-ni-ca' => '53',    'I Ti-mo-the' => '54',    'II Ti-mo-the' => '55',    'Tit' => '56',    'Phi-le-mon' => '57',    'He-bo-ro' => '58',    'Gia-co' => '59',    'I Phi-e-ro' => '60',    'II Phi-e-ro' => '61',    'I Giang' => '62',    'II Giang' => '63',    'III Giang' => '64',    'Giu-de' => '65',    'Khai-huyen' => '66',
];

function renameFilesInDir($dirPath, $booksMap)
{
    if (! is_dir($dirPath)) {
        echo "Directory not found: $dirPath\n";

        return;
    }

    $files = scandir($dirPath);
    $renamedCount = 0;

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.gitkeep') {
            continue;
        }
        if (is_dir($dirPath.'/'.$file)) {
            continue;
        }

        // Skip if already has correct prefix (e.g. 01_Sang_The_Ky_1.txt)
        if (preg_match('/^\d{2}_/', $file)) {
            continue;
        }

        // Pattern matching the Book Name
        // Filename looks like: Sang_The_Ky_1.txt or I_Co_rintô_12.txt
        $matchedBook = null;
        $longestMatch = 0;
        foreach ($booksMap as $bookKey => $prefix) {
            if (strpos($file, $bookKey.'_') === 0) {
                if (strlen($bookKey) > $longestMatch) { // prevent 'Giang' matching before 'I_Giăng' if edge case
                    $matchedBook = $bookKey;
                    $longestMatch = strlen($bookKey);
                }
            }
        }

        if ($matchedBook) {
            $prefix = $booksMap[$matchedBook];
            $newName = $prefix.'_'.$file;
            rename($dirPath.'/'.$file, $dirPath.'/'.$newName);
            $renamedCount++;
        } else {
            echo "Unmatched file: $file\n";
        }
    }
    echo "Renamed $renamedCount files in $dirPath\n";
}

$baseKinhThanh = __DIR__.'/tai-lieu/kinh-thanh';
$baseGiaiNghia = __DIR__.'/tai-lieu/kinh-thanh-giai-nghia';

echo "Processing Kinh Thanh...\n";
renameFilesInDir($baseKinhThanh, $books);

echo "Processing Kinh Thanh Giai Nghia...\n";
renameFilesInDir($baseGiaiNghia, $books);

echo "\nDone!\n";
