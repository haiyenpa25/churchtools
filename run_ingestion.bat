@echo off
chcp 65001 > nul
echo ====================================================
echo   KNOWLEDGE GRAPH AUTO AI PIPELINE (LOCAL TO CLOUD)
echo ====================================================
echo Day la Phien ban dac biet theo Yeu cau cua Giam Doc Du An!
echo Co che hoat dong: Local Windows se cAng dang toan bo chi phi
echo va thoi gian xu ly AI. Sau do tu dong dong goi len Server.
echo.

echo [1] Xoa sach du lieu Database cu tren may tinh nay...
php artisan bible:reset --force

echo.
echo [2] Bat dau day hang loat Tai Lieu vao Queue...
php artisan bible:ingest --category=kinh-thanh

echo.
echo [3] 🚀 KICH HOAT HANG TRAM CON BOT AI (QUEUE WORKER)...
echo Tien trinh nay se tu dong doc tung file. NeU loi 429 se tu cho 15s.
echo Vui long tha may tinh o day va di uong Coffee nhe anh!
php artisan queue:work --stop-when-empty

echo.
echo [4] Dong goi CSDL Vang ngoc nay thanh cac file JSON (Dump)...
php artisan bible:export-dump

echo.
echo [5] Tu dong dong bo (Push) Data JSON nay len Github...
git add database/data/bible_dump/*
git commit -m "Auto: Day CSDL AI Graph JSON tu Local len Cloud"
git push origin main

echo.
echo ====================================================
echo 🎉 HOAN THANH XUAT SAC!
echo ----------------------------------------------------
echo Bay gio anh chi len Website tren Hosting:
echo 1. Vao muc [Cai Dat He Thong]
echo 2. Vao Tab [Di Chuyen Du Lieu (Dump ^& Restore)]
echo 3. Bam nut "Nap du lieu Git" (Mau xanh duong)
echo La toan bo cong suc AI chay se duoc phuc hoi tren Server!
echo P/S: Server se khong con bi qua tai nua!
echo ====================================================
pause
