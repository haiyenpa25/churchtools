<?php

namespace Modules\BibleLearning\Services;

use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Http;

class RagScraperService
{
    /**
     * Hàm lấy Text tinh khiết từ kinhthanh.httlvn.org/?v=VI1934 và các trang mục vụ uy tín khác.
     * Đây là Data Pipeline "đút ăn" cho con AI Gemini để tránh bệnh ảo giác.
     *
     * @param  string  $url  Url trích xuất
     * @return string Văn bản Kinh Thánh tinh gọn.
     *
     * @throws Exception
     */
    public function scrapeArticle(string $url): string
    {
        $response = Http::timeout(10)->get($url);

        if (! $response->successful()) {
            throw new Exception('Crawler thất bại. HTTP Status: '.$response->status());
        }

        $html = $response->body();

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // KIẾN TRÚC MŨI KHOAN MỤC TIÊU (TARGETED PARSERS)
        // 1. Kinhthanh.httlvn.org (Bản truyền thống 1934)
        if (str_contains($url, 'kinhthanh.httlvn.org')) {
            $contentNodes = $xpath->query('//div[contains(@class, "bible-content")]');
        } 
        // 2. Httlvn.org (Tin tức Tổng Liên Hội)
        elseif (str_contains($url, 'httlvn.org')) {
            $contentNodes = $xpath->query('//div[contains(@class, "entry-content")] | //article');
        }
        // 3. Httlvinhphuoc.org (Hội Thánh Vĩnh Phước)
        elseif (str_contains($url, 'httlvinhphuoc.org')) {
            $contentNodes = $xpath->query('//div[contains(@id, "ContentDetail")] | //div[contains(@class, "content-detail")]');
        }
        // 4. Vietchristian.com (Thư Viện Cơ Đốc)
        elseif (str_contains($url, 'vietchristian.com')) {
            $contentNodes = $xpath->query('//div[contains(@class, "Paragraph")] | //blockquote | //p');
        } 
        // 5. Fallback tổng quát cho mọi trang
        else {
            $contentNodes = $xpath->query('//main | //article | //div[contains(@class, "content")] | //body');
        }

        // Đọc node
        if ($contentNodes->length === 0) {
            $contentNodes = $xpath->query('//body');
        }

        $rawText = '';
        foreach ($contentNodes as $node) {
            $rawText .= $node->textContent.' ';
        }

        // Loại bỏ Script và Style dư thừa
        $rawText = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $rawText);
        $rawText = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $rawText);
        
        // Dọn cỏ khoảng trắng
        $cleanText = preg_replace('/\s+/', ' ', $rawText);
        
        // Cắt bớt nếu văn bản quá khủng (tránh tràn 32k Token của Gemini limit local)
        $cleanText = mb_substr(trim($cleanText), 0, 15000);

        return $cleanText;
    }
}
