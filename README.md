# The Cafe Library Website

A responsive website for **The Cafe Library**, a bookshelf cafe in Vapi, Gujarat. This is a student practice project and is **not** the cafe's official website.

**Live demo:** https://jacky09876.github.io/cafe-library-website/ (works once GitHub Pages is turned on for the repository)

Add a `screenshot.png` of the home page to the repository root and it will show here:

![Home page](screenshot.png)

## Features
- Responsive layout for phone, tablet and desktop
- Menu page with category filters, built from one data list
- Photo gallery with a click-to-enlarge view
- Contact page with an embedded Google Map and a directions button
- Contact form that opens WhatsApp with the message ready to send
- Cart and ordering: add items from the menu, place an order, then pay at the cafe
- Reviews page with star ratings
- Admin dashboard, kitchen screen and printable bill (PHP + MySQL mode)
- Warm colours taken from the cafe's own photos (espresso brown, amber, cream, teal)
- Keyboard focus styles and reduced-motion support

## Pages
| Page | File |
|------|------|
| Home | `index.html` |
| Menu | `menu.html` |
| About | `about.html` |
| Gallery | `gallery.html` |
| Contact | `contact.html` |
| Order | `order.html` |
| Reviews | `reviews.html` |
| Pay | `pay.html` |

## Built with
HTML5, CSS3 (Grid and Flexbox), vanilla JavaScript, Google Fonts (Fraunces, Karla), Google Maps embed.

## Run it locally
1. Download or clone this repository.
2. Open `index.html` in Chrome or Edge.

## Customize
All cafe details are at the top of `js/main.js`: address, phone, WhatsApp number, opening hours, Instagram link, menu items and prices.

## Project structure
```
cafe-website/
├── index.html, menu.html, about.html, gallery.html, contact.html
├── order.html, reviews.html, pay.html
├── css/style.css
├── js/main.js
├── images/
├── api/        order.php, review.php, config.sample.php
├── admin/      login.php, index.php, kitchen.php, bill.php, logout.php
├── database.sql
└── start-kitchen-printing*.bat
```

## Credits
Photos are cropped from the cafe's public Google Maps listing and are used here only as placeholders for learning. They belong to their owners. Replace them with the cafe's own photos before any real use.

## Author
**Jacky Kumar**, BCA (Hons.) student, Vapi, Gujarat.

## License
Code is released under the MIT License (see `LICENSE`). Photos are not covered by this license.

## How it works in the cafe (PHP + MySQL mode)
1. **Order:** the customer opens the website on their phone, adds items, enters their table number and sends the order.
2. **Kitchen:** the order prints on the roll printer in the kitchen. The kitchen computer keeps `admin/kitchen.php` open, and new orders print automatically.
3. **Bill and payment:** after the meal, staff open the order in the admin, show the bill, and mark it paid by cash, UPI (QR on screen) or card.
4. **Reviews:** customers write reviews on the Reviews page. They are saved in MySQL, shown to everyone, and the latest three appear on the home page. Admin can hide a review.

### Setup (XAMPP)
1. Copy this folder to `C:\xampp\htdocs\cafe-website`, start Apache and MySQL.
2. In phpMyAdmin, import `database.sql` (it also upgrades an older database, on both MySQL and MariaDB).
3. Copy `api/config.sample.php` to `api/config.php` and change the admin password.
4. Open the site at `http://localhost/cafe-website/`. `js/main.js` detects this automatically and turns on server mode (it uses demo mode on GitHub Pages or when `index.html` is double-clicked).
5. Log in at `/admin/login.php`, then open `/admin/kitchen.php` on the kitchen computer.

### Roll printer
- Install the thermal printer (58 mm or 80 mm) in Windows and set it as the default printer.
- Double-click `start-kitchen-printing.bat` (Edge) or `start-kitchen-printing-chrome.bat` on the kitchen computer. It opens the kitchen page with silent printing turned on. Use "Print a test ticket" to check the printer. Set `PAPER_MM` in `api/config.php` to 58 for small printers.
- Start Chrome with `--kiosk-printing` (add it to the shortcut's Target) so tickets print without a dialog.
- Customers must reach the server: use the cafe Wi-Fi and open `http://<cafe-computer-ip>/cafe-website/`, or host the folder on a PHP host.

### GitHub Pages demo
GitHub Pages cannot run PHP. With `api: ""` the site works as a demo: orders go to WhatsApp, and reviews stay in the visitor's browser.

## Files not included in this repository
- `api/config.php`: copy `api/config.sample.php` to `api/config.php` and set your own database and admin password.
- `api/test.php`: an optional setup checker for a local XAMPP server (listed in `.gitignore`).
- `images/upi-qr.png` is a placeholder. Replace it with your own UPI QR image before using the Pay page.
