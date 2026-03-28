<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\BibleLearning\Services\OllamaTrackingService;

class OllamaController extends Controller
{
    public function getStatus(OllamaTrackingService $tracker)
    {
        return response()->json($tracker->getStatus());
    }

    public function startPipeline(Request $request, OllamaTrackingService $tracker)
    {
        $status = $tracker->getStatus();
        if ($status['is_running']) {
            return response()->json(['error' => 'Pipeline AI đang hoạt động rồi!'], 400);
        }

        $files = $request->input('files'); // Nhận danh sách mảng Array File từ VueJS
        $model = $request->input('model', 'gemma3:4b'); // Nhận Model từ giao diện, mặc định gemma3:4b

        try {
            // Khởi động tiến trình ngầm không chờ (Daemon Process) dưới dạng Windows CMD
            // Sử dụng c:\xampp\php\php.exe để đảm bảo môi trường XAMPP tuyệt đối
            $phpPath = 'c:\xampp\php\php.exe';
            if (! file_exists($phpPath)) {
                $phpPath = 'php'; // Fallback nếu không có XAMPP
            }

            // [Lỗi Không tìm thấy artisan]: PHP của XAMPP chạy file từ thư mục root của web (ví dụ: public/), phải khai báo đường dẫn tuyệt đối bằng base_path()
            $artisanPath = escapeshellarg(base_path('artisan'));
            $executeCmd = "start /B {$phpPath} {$artisanPath} bible:ollama-pipeline --category=kinh-thanh --model=".escapeshellarg($model);

            // Lọc đích danh các file cần chạy
            if (! empty($files) && is_array($files)) {
                foreach ($files as $file) {
                    $executeCmd .= ' --file='.escapeshellarg($file);
                }
            }

            pclose(popen($executeCmd, 'r'));

            // Đánh dấu Tracking để UI phản hồi ngay lập tức
            $tracker->start();

            return response()->json([
                'success' => true,
                'message' => 'Cỗ máy AI Pipeline đã kích hoạt dạng ngầm!',
                'command_executed' => $executeCmd,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi kích hoạt tiến trình: '.$e->getMessage()], 500);
        }
    }

    public function getFiles()
    {
        $path = database_path('data/bible_dump');
        if (! is_dir($path)) {
            return response()->json([]);
        }

        $files = array_diff(scandir($path), ['.', '..']);
        $result = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $fullPath = $path.DIRECTORY_SEPARATOR.$file;
                $result[] = [
                    'name' => $file,
                    'size' => round(filesize($fullPath) / 1024 / 1024, 2).' MB',
                    'updated_at' => date('Y-m-d H:i:s', filemtime($fullPath)),
                    'timestamp' => filemtime($fullPath),
                ];
            }
        }

        // Sắp xếp file mới nhất lên đầu
        usort($result, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return response()->json($result);
    }

    public function commitAndPush()
    {
        try {
            $gitPath = '"C:\Program Files\Git\bin\git.exe"';

            // Xây dựng chuỗi lệnh Automation
            $cmd = 'cd '.base_path()." && $gitPath add database/data/bible_dump/ && $gitPath commit -m \"Auto Export: Knowledge Graph JSON Dump tu Local AI\" && $gitPath push origin main 2>&1";

            $output = shell_exec($cmd);

            Log::info('Web Auto Git Push Output: '.$output);

            if (strpos($output, 'fatal:') !== false || strpos($output, 'error:') !== false) {
                return response()->json(['success' => false, 'message' => 'Lỗi Git: '.$output], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã đóng gói thư mục bible_dump và đẩy thẳng lên Server Cloud (Git) thành công!',
                'log' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Exception: '.$e->getMessage()], 500);
        }
    }
}
