<?php

$bookTitles = [
    '01' => 'Sang-the-ky',
    '02' => 'Xuat-e-dip-to-ky',
    '03' => 'Le-vy-ky',
    '04' => 'Dan-so-ky',
    '05' => 'Phuc-truyen-luat-le-ky',
    '06' => 'Gio-sue',
    '07' => 'Cac-quan-xet',
    '08' => 'Ru-to',
    '09' => 'I Sa-mu-en',
    '10' => 'II Sa-mu-en',
    '11' => 'I Cac-vua',
    '12' => 'II Cac-vua',
    '13' => 'I Su-ky',
    '14' => 'II Su-ky',
    '15' => 'E-xo-ra',
    '16' => 'Ne-he-mi',
    '17' => 'E-xo-te',
    '18' => 'Giop',
    '19' => 'Thi-thien',
    '20' => 'Cham-ngon',
    '21' => 'Truyen-dao',
    '22' => 'Nha-ca',
    '23' => 'E-sai',
    '24' => 'Gie-re-mi',
    '25' => 'Ca-thuong',
    '26' => 'E-xe-chi-en',
    '27' => 'Da-ni-en',
    '28' => 'O-se',
    '29' => 'Gio-en',
    '30' => 'A-mot',
    '31' => 'Ap-dia',
    '32' => 'Gio-na',
    '33' => 'Mi-che',
    '34' => 'Na-hum',
    '35' => 'Ha-ba-cuc',
    '36' => 'So-pho-ni',
    '37' => 'A-ghe',
    '38' => 'Xa-cha-ri',
    '39' => 'Ma-la-chi',
    '40' => 'Ma-thi-o',
    '41' => 'Mac',
    '42' => 'Lu-ca',
    '43' => 'Giang',
    '44' => 'Cong-vu-cac-su-do',
    '45' => 'Ro-ma',
    '46' => 'I Co-rinh-to',
    '47' => 'II Co-rinh-to',
    '48' => 'Ga-la-ti',
    '49' => 'E-phe-so',
    '50' => 'Phi-lip',
    '51' => 'Co-lo-se',
    '52' => 'I Te-sa-lo-ni-ca',
    '53' => 'II Te-sa-lo-ni-ca',
    '54' => 'I Ti-mo-the',
    '55' => 'II Ti-mo-the',
    '56' => 'Tit',
    '57' => 'Phi-le-mon',
    '58' => 'He-bo-ro',
    '59' => 'Gia-co',
    '60' => 'I Phi-e-ro',
    '61' => 'II Phi-e-ro',
    '62' => 'I Giang',
    '63' => 'II Giang',
    '64' => 'III Giang',
    '65' => 'Giu-de',
    '66' => 'Khai-huyen',
];

$dirPath = __DIR__.'/tai-lieu/kinh-thanh';
$files = glob($dirPath.'/*.txt');
$count = 0;
foreach ($files as $f) {
    if (is_dir($f)) {
        continue;
    }
    $base = basename($f);
    if (preg_match('/^(\d{2})_.*?_(\d+)\.txt$/', $base, $m)) {
        $prefix = $m[1];
        $chap = $m[2];
        if (isset($bookTitles[$prefix])) {
            $newName = $prefix.'_'.$bookTitles[$prefix].'_'.$chap.'.txt';
            if ($base !== $newName) {
                rename($f, $dirPath.'/'.$newName);
                $count++;
            }
        }
    }
}
echo "Renamed $count files in Kinh Thanh.\n";
