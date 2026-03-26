<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BibleFlow AI — Kinh Thánh Karaoke</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background: #0B132B; color: #F8FAFC; font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Georgia', serif; }
        .word { transition: color 0.15s ease, text-shadow 0.15s ease; cursor: pointer; display: inline-block; margin-right: 0.3em; margin-bottom: 0.3em;}
        /* Active Karaoke highlight */
        .word.active { color: #FFD700; border-bottom: 4px solid #FFD700; text-shadow: 0 0 15px rgba(255,215,0,0.8); }
        /* Correct / Finished words */
        .word.correct { color: #34D399; text-shadow: 0 0 12px rgba(52,211,153,0.6); }
        
        #reading-area { display: none; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-slate-900 via-[#0B132B] to-slate-900">

    <div class="w-full max-w-4xl z-10">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ url('/') }}" class="text-white hover:text-emerald-400 transition flex items-center gap-2">
                <span>←</span> Quay lại Portal
            </a>
            <div class="flex items-center gap-3">
                <span class="font-bold text-xl tracking-tight flex items-center gap-2">
                    🎤 <span class="text-white">BibleFlow</span><span class="text-emerald-400">AI</span>
                </span>
            </div>
            <div class="flex items-center gap-2 text-sm text-emerald-400 font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Web Speech API v2
            </div>
        </div>

        <!-- Setup Area -->
        <div id="setup-area" class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10 shadow-2xl transition-all">
            <h2 class="text-2xl font-bold mb-2 text-emerald-400 text-center">Luyện Đọc Kinh Thánh (Phiên bản Pro Tối Ưu)</h2>
            <p class="text-center text-slate-400 mb-6">Sử dụng công nghệ AI Google của trình duyệt (Nhận diện siêu tốc, bỏ qua lỗi dấu tiếng Việt)</p>
            
            <textarea id="inputText" rows="6" 
                class="w-full bg-slate-900/50 border border-slate-700 rounded-xl p-4 text-white text-lg focus:outline-none focus:border-emerald-500 transition-colors mb-6 placeholder-slate-500 font-serif" 
                placeholder="Dán đoạn Kinh Thánh vào đây... (Hỗ trợ dán cả số câu)"></textarea>
            
            <button onclick="startLesson()" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-xl text-xl transition-all hover:scale-[1.02] shadow-lg shadow-emerald-500/30">
                BẮT ĐẦU ĐỌC
            </button>
        </div>

        <!-- Reading Area -->
        <div id="reading-area" class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-10 shadow-2xl transition-all" style="display: none;">
            
            <div class="flex justify-between items-center mb-10 pb-6 border-b border-white/10">
                <button onclick="stopLesson()" class="px-5 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white transition-colors text-sm font-medium">
                    ⏹ Dừng lại
                </button>
                
                <div id="status" class="flex-1 text-center font-medium text-emerald-300 mx-4">
                    Sẵn sàng...
                </div>
                
                <button onclick="skipWord()" class="px-5 py-2 rounded-lg bg-emerald-600/30 border border-emerald-500/50 hover:bg-emerald-600/50 text-emerald-300 transition-colors text-sm font-medium">
                    ⏭ Bỏ qua từ khó
                </button>
            </div>

            <!-- The Text -->
            <div id="reading-content" class="text-5xl font-serif italic leading-[1.6] select-none text-slate-400 text-center">
            </div>

        </div>
    </div>

<script>
    let wordsArray = [];
    let currentWordIndex = 0;
    let recognition;
    let isListening = false;
    let lastSpokenCount = 0; // Tracks how many spoken words we've successfully mapped

    const setupArea = document.getElementById('setup-area');
    const readingArea = document.getElementById('reading-area');
    const inputText = document.getElementById('inputText');
    const readingContent = document.getElementById('reading-content');
    const statusDisplay = document.getElementById('status');

    // Mặc định câu test
    inputText.value = "Chắc chắn, sự thật thường làm tổn thương. Nhưng sự thật lại RẤT CẦN THIẾT. Đó là một vết thương của lòng tin. Nó giải phóng con người. Khi tôi học lớp bảy, tôi đã phẫu thuật thoát vị...";

    function removeAccents(str) {
        return str.normalize('NFD')
                  .replace(/[\u0300-\u036f]/g, '')
                  .replace(/đ/g, 'd').replace(/Đ/g, 'D')
                  .toLowerCase();
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (SpeechRecognition) {
        recognition = new SpeechRecognition();
        recognition.lang = 'vi-VN';
        recognition.continuous = true;
        recognition.interimResults = true;

        recognition.onresult = (event) => {
            let transcript = "";
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                transcript += event.results[i][0].transcript;
            }
            const spokenText = transcript.trim();
            statusDisplay.innerHTML = `🎤 Đang nghe: <span class="text-white">"${spokenText}"</span>`;
            checkReading(spokenText);
        };

        recognition.onend = () => { if (isListening) recognition.start(); };
    } else {
        alert("Trình duyệt của bạn không hỗ trợ Web Speech API. Vui lòng dùng Google Chrome hoặc Microsoft Edge.");
    }

    function startLesson() {
        if (!SpeechRecognition) return;
        let rawText = inputText.value.trim();
        if (!rawText) { alert("Vui lòng dán nội dung trước nhé!"); return; }

        let cleanText = rawText.replace(/\b\d+\b/g, '').replace(/\s+/g, ' ').trim();
        wordsArray = cleanText.split(/\s+/);

        readingContent.innerHTML = '';
        wordsArray.forEach((word, index) => {
            const span = document.createElement('span');
            span.innerText = word;
            span.id = `w-${index}`;
            span.className = 'word';
            readingContent.appendChild(span);
        });

        currentWordIndex = 0;
        lastSpokenCount = 0;
        updateHighlight();
        setupArea.style.display = 'none';
        readingArea.style.display = 'block';

        isListening = true;
        recognition.start();
        statusDisplay.innerText = "🎤 Bắt đầu đọc đi nào...";
    }

    function getEditDistance(a, b) {
        if (a.length === 0) return b.length; 
        if (b.length === 0) return a.length; 
        var matrix = [];
        for (var i = 0; i <= b.length; i++) matrix[i] = [i];
        for (var j = 0; j <= a.length; j++) matrix[0][j] = j;
        for (var i = 1; i <= b.length; i++) {
            for (var j = 1; j <= a.length; j++) {
                if (b.charAt(i-1) == a.charAt(j-1)) {
                    matrix[i][j] = matrix[i-1][j-1];
                } else {
                    matrix[i][j] = Math.min(matrix[i-1][j-1] + 1, Math.min(matrix[i][j-1] + 1, matrix[i-1][j] + 1));
                }
            }
        }
        return matrix[b.length][a.length];
    }

    function checkReading(spokenText) {
        if (currentWordIndex >= wordsArray.length) return;

        let spokenNoAccent = removeAccents(spokenText);
        let spokenWords = spokenNoAccent.trim().split(/\s+/).filter(Boolean);

        // If API restarted the string (or final result was committed and new interim starts)
        if (spokenWords.length < lastSpokenCount) {
            lastSpokenCount = 0;
        }

        let matchedSomething = false;

        while (currentWordIndex < wordsArray.length) {
            // Only search in the newly spoken words we haven't mapped yet
            let newSpokenWords = spokenWords.slice(lastSpokenCount);
            if (newSpokenWords.length === 0) break;

            // CHẾ ĐỘ HUẤN LUYỆN KHẮC NGHIỆT (STRICT MODE): 
            // KHÔNG LOOKAHEAD. Bắt buộc đọc đúng chữ hiện tại mới được đi tiếp.
            let targetIndex = currentWordIndex;
            let rawTarget = wordsArray[targetIndex];
            let targetWord = rawTarget.replace(/[.,/#!$%^&*;:{}=\-_`~()]/g,"");
            let targetNoAccent = removeAccents(targetWord);

            // Search in the next 7 spoken words to allow user to stutter or say filler words
            let searchLimit = Math.min(7, newSpokenWords.length);
            let foundIdx = -1;
            let consumed = 1;

            // Hàm cào bằng ngữ âm tiếng Việt
            const pNorm = (s) => s.replace(/s/g, 'x').replace(/tr/g, 'ch').replace(/gi/g, 'd').replace(/r/g, 'd');

            for (let k = 0; k < searchLimit; k++) {
                let w1 = newSpokenWords[k];
                let w2 = (k + 1 < newSpokenWords.length) ? newSpokenWords[k+1] : "";
                let w3 = (k + 2 < newSpokenWords.length) ? newSpokenWords[k+2] : "";
                let w4 = (k + 3 < newSpokenWords.length) ? newSpokenWords[k+3] : "";
                
                let c2 = w1 + w2;
                let c3 = w1 + w2 + w3;
                let c4 = w1 + w2 + w3 + w4;

                // Các phát âm dị biệt của Google Speech API đối với tên riêng Kinh Thánh
                let aliases = [targetNoAccent];
                if (targetNoAccent === "jesus") aliases.push("giesu", "gie", "su", "je", "ye", "dieu", "ze");
                if (targetNoAccent === "christ") aliases.push("rit", "rich", "ris", "kris", "co", "rac", "ri", "quy", "huy", "khi", "chri", "chris", "crip", "cruit");
                if (targetNoAccent === "phiero") aliases.push("phiero", "phia", "phio");

                for (let alias of aliases) {
                    let aNorm = pNorm(alias);
                    let w1Norm = pNorm(w1);
                    
                    if (w1Norm === aNorm || (aNorm.length >= 3 && w1Norm.includes(aNorm))) {
                        foundIdx = k; consumed = 1; break;
                    }
                    if (w2 && (pNorm(c2) === aNorm || (aNorm.length >= 4 && pNorm(c2).includes(aNorm)))) {
                        foundIdx = k; consumed = 2; break;
                    }
                    if (w3 && (pNorm(c3) === aNorm || (aNorm.length >= 5 && pNorm(c3).includes(aNorm)))) {
                        foundIdx = k; consumed = 3; break;
                    }
                    if (w4 && (pNorm(c4) === aNorm || (aNorm.length >= 6 && pNorm(c4).includes(aNorm)))) {
                        foundIdx = k; consumed = 4; break;
                    }
                    
                    if (aNorm.length >= 5 && w2 && getEditDistance(pNorm(c2), aNorm) <= 2) {
                        foundIdx = k; consumed = 2; break;
                    }
                    if (aNorm.length >= 6 && w3 && getEditDistance(pNorm(c3), aNorm) <= 2) {
                        foundIdx = k; consumed = 3; break;
                    }
                }
                if (foundIdx !== -1) break;
            }

            if (foundIdx !== -1) {
                // Đọc trúng! Kích hoạt từ hiện tại.
                const el = document.getElementById(`w-${currentWordIndex}`);
                if (el) { 
                    el.classList.remove('active');
                    el.classList.add('correct'); 
                }
                currentWordIndex++; // Tiến tới từ tiếp theo một cách tuần tự
                lastSpokenCount += (foundIdx + consumed);
                matchedSomething = true;
            } else {
                // Không tìm thấy chữ hiện tại trong các chữ vừa đọc.
                // STOP tại đây. Bắt buộc người dùng phải nặn ra đúng chữ này hoặc bóp nút "Bỏ qua".
                break;
            }
        }

        if (matchedSomething) {
            updateHighlight();
        }

        if (currentWordIndex >= wordsArray.length) {
            statusDisplay.innerHTML = "🎉 <span class='text-emerald-400 font-bold'>A-men! Bạn đã đọc xong rồi!</span>";
            isListening = false;
            recognition.stop();
        }
    }

    function updateHighlight() {
        const words = document.querySelectorAll('.word');
        words.forEach(w => w.classList.remove('active'));
        const next = document.getElementById(`w-${currentWordIndex}`);
        if (next) {
            next.classList.add('active');
        }
    }

    function skipWord() {
        if (currentWordIndex < wordsArray.length) {
            const el = document.getElementById(`w-${currentWordIndex}`);
            if (el) el.className = 'word correct';
            currentWordIndex++;
            updateHighlight();
        }
    }

    function stopLesson() {
        isListening = false;
        recognition.stop();
        setupArea.style.display = 'block';
        readingArea.style.display = 'none';
    }
</script>

</body>
</html>
