<?php

namespace Modules\BibleLearning\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Lắng nghe tín hiệu Push từ kho chứa GitHub (GitHub Webhooks Payload)
     */
    public function github(Request $request)
    {
        // 1. Kiểm tra sự tồn tại của Header chứa Chữ ký bảo mật do GitHub nhúng vào
        $signature = $request->header('X-Hub-Signature-256');
        if (!$signature) {
            return response()->json(['error' => 'Header X-Hub-Signature-256 is missing'], 400);
        }

        // 2. Load khóa bí mật GITHUB_WEBHOOK_SECRET từ file .env
        $secret = env('GITHUB_WEBHOOK_SECRET');
        if (!$secret) {
            Log::error('GitHub Webhook Error: GITHUB_WEBHOOK_SECRET is not set in .env');
            return response()->json(['error' => 'Secret not configured on Server'], 500);
        }

        // 3. Tính toán lại mã băm SHA-256 từ Body Request dựa trên khóa bí mật của mình
        $payload = $request->getContent();
        $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        // 4. So sánh 2 chữ ký: Nếu Khớp -> Đích thị là GitHub gọi. Nếu Sai -> Hacker giả mạo.
        if (!hash_equals($hash, $signature)) {
            Log::error('GitHub Webhook Error: Invalid signature / Hacker Attack Detected');
            return response()->json(['error' => 'Invalid signature / Forbidden'], 403);
        }

        // 5. Cấp quyền xử lý: Chỉ tiến hành nhúng Code mới khi Event là PUSH vào nhánh MAIN
        $event = $request->header('X-GitHub-Event');
        if ($event === 'push') {
            $data = json_decode($payload, true);
            $branch = isset($data['ref']) ? explode('/', $data['ref'])[2] : '';

            if ($branch === 'main' || $branch === 'master') {
                Log::info('GitHub Webhook: Push to main/master detected. Starting Auto-Deploy...');
                
                // Kích hoat File Bash Script siêu tốc chạy tự động
                $scriptPath = base_path('webhook.sh');
                
                // Thực thi Terminal System. Khóa hậu tố 2>&1 để hứng toàn bộ output kể cả Lỗi
                $output = shell_exec("bash " . escapeshellarg($scriptPath) . " 2>&1");
                Log::info("GitHub Webhook Deploy Output: \n" . $output);

                return response()->json(['message' => 'Deployed successfully', 'output' => $output]);
            }
        }

        return response()->json(['message' => 'Skipped. Not a push to main/master branch.']);
    }
}
