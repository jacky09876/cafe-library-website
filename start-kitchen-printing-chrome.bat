@echo off
rem Same as start-kitchen-printing.bat, but for Google Chrome.
start "" chrome --kiosk-printing --user-data-dir="%USERPROFILE%\cafe-kitchen-profile" "http://localhost/cafe-website/admin/kitchen.php"
