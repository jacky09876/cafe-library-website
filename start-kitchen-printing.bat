@echo off
rem Opens the kitchen screen in Edge so new orders print by themselves, with no print dialog.
rem Before using: set the roll printer as the Windows default printer, and start Apache and MySQL in XAMPP.
rem The first time, log in at the page. The login is remembered in this separate browser profile.
start "" msedge --kiosk-printing --user-data-dir="%USERPROFILE%\cafe-kitchen-profile" "http://localhost/cafe-website/admin/kitchen.php"
