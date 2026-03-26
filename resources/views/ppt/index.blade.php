<!DOCTYPE html>
<html lang="vi">
@include('ppt._partials.head')
<body class="bg-gray-900 text-white min-h-screen relative overflow-x-hidden p-6" x-data="pptGenerator()">

    {{-- Background Gradient --}}
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
    <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-10%] left-[20%] w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-4000"></div>

    <div class="container mx-auto relative z-10 max-w-4xl pt-10">

        {{-- Header --}}
        <header class="text-center mb-12">
            <div class="flex justify-between items-start mb-2">
                <a href="/ChurchTool/public/" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">← Portal</a>
                <div class="flex items-center gap-2">
                    <a href="/ChurchTool/public/ppt/sermon"
                       class="inline-flex items-center gap-1.5 bg-gray-800/80 hover:bg-gray-700 border border-gray-700 hover:border-yellow-500 text-gray-300 hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200">
                        📖 Bài Giảng
                    </a>
                    <a href="/ChurchTool/public/ppt/templates"
                       class="inline-flex items-center gap-1.5 bg-gray-800/80 hover:bg-gray-700 border border-gray-700 hover:border-purple-500 text-gray-300 hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200">
                        🗂️ Quản lý Template
                    </a>
                    <a href="/ChurchTool/public/ppt/layout-editor"
                       class="inline-flex items-center gap-1.5 bg-gray-800/80 hover:bg-gray-700 border border-gray-700 hover:border-teal-500 text-gray-300 hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-200">
                        🎨 Thiết kế Layout
                    </a>
                </div>


            <h1 class="text-5xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-500 mb-3">
                PptLivestream
            </h1>
            <p class="text-lg text-gray-400 font-light">Tự động sinh Layout và chia nhánh PowerPoint siêu tốc bằng Python AI</p>
        </header>

        {{-- Main Glass Panel --}}
        <div class="glass-panel rounded-3xl p-8 shadow-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                @include('ppt._partials.step1_input')
                @include('ppt._partials.step2_wysiwyg')
            </form>
        </div>

        @include('ppt._partials.toasts')
    </div>

    <script src="/ChurchTool/public/js/ppt/ppt_generator.js"></script>
</body>
</html>
