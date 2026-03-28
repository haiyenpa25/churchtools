<?php
$json = file_get_contents('https://generativelanguage.googleapis.com/v1beta/models?key=AIzaSyBqiMuE2fFPqmz6Ut5BJkeS4D5LkGDqesI');
$data = json_decode($json, true);
foreach($data['models'] as $m) {
    if(strpos($m['name'], 'gemini-2') !== false || strpos($m['name'], 'gemini-1.5') !== false) {
        echo $m['name'] . "\n";
    }
}
