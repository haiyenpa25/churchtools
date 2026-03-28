<?php

use GuzzleHttp\Client;

require __DIR__.'/vendor/autoload.php';

$client = new Client;

$text = 'Ban đầu Đức Chúa Trời dựng nên trời đất.';
$context = 'Bối cảnh: Cuốn sách 01_Sang-the-ky, chương 01_Sang-the-ky_1';

$prompt = "Bạn là Kỹ sư Dữ liệu (Data Engineer) và Chuyên gia Thần học.\n".
"Nhiệm vụ của bạn là trích xuất Thực thể (Nodes) và Mối quan hệ (Edges) từ văn bản dưới đây.\n\n".
"Quy tắc Đầu ra BẮT BUỘC là ĐÚNG ĐỊNH DẠNG JSON MẢNG (Array of JSON Objects):\n".
"[\n".
"  { \"type\": \"node\", \"raw_data\": { \"label\": \"Tên\", \"group\": \"nhan_vat|dia_diem|su_kien|khai_niem\", \"description\": \"Mô tả\" } },\n".
"  { \"type\": \"edge\", \"raw_data\": { \"source_node_id\": \"Tên 1\", \"target_node_id\": \"Tên 2\", \"relationship\": \"Quan hệ\" } }\n".
"]\n\n".
"Bối cảnh (Context): $context\n\n".
"Hãy trích xuất từ văn bản sau:\n$text\n\n".
'Đầu ra JSON Mảng sạch sẽ, KHÔNG BỌC TRONG ```json, CHỈ TRẢ VỀ JSON MẢNG BẮT ĐẦU BẰNG DẤU [.';

try {
    $res = $client->post('http://127.0.0.1:11434/api/generate', [
        'json' => [
            'model' => 'qwen2.5:3b',
            'prompt' => $prompt,
            'stream' => false,
            'format' => 'json',
        ],
    ]);
    $body = $res->getBody()->getContents();
    echo "Phản hồi thô:\n$body\n\n";

    $json = json_decode($body, true);

    $result = $json['response'];
    echo "=====================\n";
    echo "Response phần Core:\n$result\n";
    echo "=====================\n";

} catch (Exception $e) {
    echo 'Lỗi: '.$e->getMessage();
}
