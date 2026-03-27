@echo off
echo =======================================================
echo.   TOOL DAY CODE LEN GITHUB TUDONG (LOCAL PUSH)
echo =======================================================
echo.
git status
git add .
git commit -m "Auto Deploy: Cập nhật hệ thống"
git push origin main
echo.
echo HOAN TAT! Neu co loi, hay mo GitHub Desktop de Push tay.
pause
