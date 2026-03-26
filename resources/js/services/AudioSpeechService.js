/**
 * Trạm phát thanh Lời Chúa (Web Speech API)
 * Lấy cảm hứng từ dự án BibleFlow, chuyển đổi văn bản Kinh Thánh thành Giọng Đọc mượt mà.
 */
export const AudioSpeechService = {
  speak(text, lang = 'vi-VN') {
    if (!('speechSynthesis' in window)) {
      console.warn("Trình duyệt của bạn không hỗ trợ Web Speech API.");
      return;
    }
    
    // Ngắt ngay lập tức nếu đang đọc dở câu cũ
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = lang;
    utterance.rate = 0.95;  // Tốc độ đọc chậm rãi, trang nghiêm
    utterance.pitch = 0.95; // Giọng hơi trầm xuống một chút
    
    // Đảm bảo load được giọng Tiếng Việt tự nhiên nhất
    const voices = window.speechSynthesis.getVoices();
    const viVoice = voices.find(v => v.lang.includes('vi') && v.localService === false) 
                 || voices.find(v => v.lang.includes('vi'));
                 
    if (viVoice) {
       utterance.voice = viVoice;
    }

    window.speechSynthesis.speak(utterance);
  },
  
  stop() {
    if ('speechSynthesis' in window) {
      window.speechSynthesis.cancel();
    }
  }
};

// Khởi tạo trước danh sách giọng đọc vì API này load bất đồng bộ
if (typeof window !== 'undefined' && 'speechSynthesis' in window) {
  window.speechSynthesis.onvoiceschanged = () => {
    window.speechSynthesis.getVoices();
  };
}
