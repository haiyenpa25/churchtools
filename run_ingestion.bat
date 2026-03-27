@echo off
chcp 65001 > nul
echo ====================================================
echo   KNOWLEDGE GRAPH AUTO BIBLE INGESTION PIPELINE
echo ====================================================
echo.

echo [1] Xoa sach du lieu Database cu...
php artisan bible:reset --force

echo.
echo [2] Bat dau day 66 sach (1189 chuong) vao Queue...
php artisan bible:ingest

echo.
echo [3] Dang xu ly AI tuyen trinh ngam (Background Worker)...
echo Tien trinh nay se tu dong dung lai khi xu ly xong tat ca.
echo Vui long xoi 1 ly coffee va cho doi :D ...
php artisan queue:work --stop-when-empty

echo.
echo [4] Dong goi du lieu thanh cac file JSON...
php artisan bible:export-dump

echo.
echo ====================================================
echo HOAN THANH! Ban co the copy folder:
echo storage/app/bible_dump
echo Va upload len VPS Production roi nhe!
echo ====================================================
pause
