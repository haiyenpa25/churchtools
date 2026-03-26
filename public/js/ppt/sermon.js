// sermon.js — Alpine.js data for Bài Giảng Live feature
// Loaded by ppt/sermon.blade.php

function sermonTool() {
    return {
        step: 1,
        pdfFile: null,
        dragging: false,
        parsing: false,
        generating: false,
        parseError: '',
        genError: '',
        blocks: [],
        sermonTitle: '',
        toast: { show: false, msg: '', success: true },

        init() {},

        typeLabel(t) {
            const map = {
                section_title: '🔵 Tiêu đề phần',
                scripture:     '🟢 Kinh văn',
                origin_word:   '🟡 Nguyên ngữ',
                list:          '🟣 Danh sách',
                conclusion:    '🔴 Kết luận',
                body:          '⚫ Nội dung',
            };
            return map[t] || t;
        },

        onDrop(evt) {
            this.dragging = false;
            const file = evt.dataTransfer.files[0];
            if (file && file.type === 'application/pdf') {
                this.pdfFile = file;
                this.sermonTitle = file.name.replace(/\.pdf$/i, '');
            }
        },

        onFileChange(evt) {
            const file = evt.target.files[0];
            if (file) {
                this.pdfFile = file;
                this.sermonTitle = file.name.replace(/\.pdf$/i, '');
            }
        },

        async parsePdf() {
            if (!this.pdfFile) return;
            this.parsing = true;
            this.parseError = '';
            const fd = new FormData();
            fd.append('file', this.pdfFile);

            try {
                const res = await fetch('/ChurchTool/public/api/ppt/sermon/parse', { method: 'POST', body: fd });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    this.blocks = data.blocks.map((b, i) => ({ ...b, id: 'b_' + i }));
                    this.step = 2;
                } else {
                    this.parseError = data.message || 'Lỗi phân tích. ' + (data.raw || '');
                }
            } catch(e) {
                this.parseError = 'Lỗi kết nối: ' + e.message;
            }
            this.parsing = false;
        },

        async generate() {
            if (!this.blocks.length) return;
            this.generating = true;
            this.genError = '';
            const fd = new FormData();
            fd.append('blocks', JSON.stringify(this.blocks));
            fd.append('theme', JSON.stringify({}));

            try {
                const res = await fetch('/ChurchTool/public/ppt/sermon/generate', { method: 'POST', body: fd });
                const data = await res.json();
                if (res.ok && data.status === 'success') {
                    window.location.href = data.download_url;
                    this.showToast('✅ File đã tạo xong! Đang tải về...', true);
                } else {
                    this.genError = data.message + (data.raw ? '\n' + data.raw : '');
                    this.showToast('Lỗi tạo file: ' + data.message, false);
                }
            } catch(e) {
                this.genError = e.message;
                this.showToast('Lỗi kết nối', false);
            }
            this.generating = false;
        },

        showToast(msg, success = true) {
            this.toast = { show: true, msg, success };
            setTimeout(() => this.toast.show = false, 4000);
        },
    };
}
