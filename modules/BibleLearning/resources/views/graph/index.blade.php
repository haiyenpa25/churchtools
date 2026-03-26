<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lưới Tư Duy Thần Học - Bible Learning</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Fonts: Inter for perfect Vietnamese UTF-8 rendering -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Load CDN vis-network -->
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
    <div id="app" class="min-h-screen">
        <knowledge-graph></knowledge-graph>
    </div>
</body>
</html>
