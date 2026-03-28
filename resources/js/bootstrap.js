import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Tự động nội suy Base URL (dành cho các Server ảo XAMPP chạy Sub-folder)
if (window.appUrl) {
    window.axios.interceptors.request.use(config => {
        if (config.url && config.url.startsWith('/')) {
            config.url = window.appUrl + config.url;
        }
        return config;
    });
}
