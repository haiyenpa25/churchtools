<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bible Learning - Approval Center</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <!-- Nạp Vite vì Vue đã sẵn sàng -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Nơi Vue 3 app/component sẽ được gắn kết -->
    <div id="app" class="min-h-screen">
        <div class="p-8">
            <a href="{{ url('/') }}" class="text-blue-500 hover:underline">&larr; Quay lại trang chủ</a>
            
            <hr class="my-6">
            
            <approval-center></approval-center>
        </div>
    </div>
</body>
</html>
