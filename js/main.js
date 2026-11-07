/* ===== EDIT THESE DETAILS ===== */
const CONFIG = {
  name: "The Cafe Library",
  tagline: "Books on every wall. Shakes and mojitos at the table.",
  address: "Add full street address, Vapi, Gujarat",   // TODO
  phone: "+91 7096603739",
  whatsapp: "917096603739",
  hours: [["Monday – Friday", "Add hours"], ["Saturday – Sunday", "Add hours"]], // TODO
  instagram: "#",                                      // TODO: full Instagram URL
  lat: 20.3944172, lng: 72.8933942,
  api: (location.hostname.endsWith("github.io") || location.protocol === "file:") ? "" : "api/",   // demo mode on GitHub Pages or a double-clicked file, server mode on localhost/XAMPP. Use "" to force demo mode.
  reviewEndpoint: "",             // optional: Formspree URL so the cafe also receives reviews
  mapsLink: "https://maps.app.goo.gl/baWL3BmPpLHx12QC9"
};
/* SAMPLE prices in rupees. Replace them with the cafe's real prices. */
const MENU = [
  {n:"Chocolate Shake", price:149, c:"Shakes & Coffee", img:"shake-choc", d:"Thick, cold and chocolatey, served in a tall glass."},
  {n:"Cream Shake", price:149, c:"Shakes & Coffee", img:"shake-straw", d:"Smooth shake topped with cream and a straw."},
  {n:"Coffee", price:99, c:"Shakes & Coffee", img:"coffee", d:"A hot cup to read with."},
  {n:"Blue Mojito", price:129, c:"Mojitos", img:"mojito-blue", d:"Bright blue, minty and full of ice."},
  {n:"Mint Mojito", price:119, c:"Mojitos", img:"", d:"Fresh mint and lime over ice."},
  {n:"Burger", price:129, c:"Bites", img:"burger", d:"Sesame bun, fresh veggies, served with a side."},
  {n:"French Fries", price:99, c:"Bites", img:"fries", d:"Crisp, seasoned and shareable."},
  {n:"Pasta", price:179, c:"Bites", img:"pasta", d:"A hot plate of pasta."},
  {n:"Cheese Chilli Toast", price:149, c:"Bites", img:"toast", d:"Toasted bread with cheese and bell peppers."}
];
const GALLERY = [
  ["lounge","Cream shakes by the shelves"],["shelf-1","Bookshelf wall"],["phone","Antique telephone"],
  ["mojito-trio","Mojitos and shakes"],["shelf-3","Reading shelves"],["laptop","A table to work at"],
  ["hero","The library corner"],["stage","Stage with mic and guitars"],["shelf-2","Books by the table"],
  ["burger","Burger"],["toast","Cheese chilli toast"],["shake-choc","Chocolate shake"]
];
/* ===== SHARED LAYOUT ===== */
const PAGES = [["index.html","Home"],["menu.html","Menu"],["about.html","About"],["gallery.html","Gallery"],["reviews.html","Reviews"],["contact.html","Contact"],["order.html","Order"],["pay.html","Pay"]];
const here = (location.pathname.split("/").pop() || "index.html").replace(/^([^.]+)$/, "$1.html"); // also works when the host hides ".html"
const el = (id) => document.getElementById(id);
el("site-header").innerHTML = `<div class="wrap bar">
  <a class="brand" href="index.html"><img src="images/logo.svg" alt="">${CONFIG.name}</a>
  <button class="burger" aria-expanded="false" aria-controls="nav">Menu</button>
  <nav id="nav" aria-label="Main">${PAGES.map(([h,t]) => `<a href="${h}"${h===here?' aria-current="page"':''}>${t}${h==="order.html"?` (<span id="cc">0</span>)`:""}</a>`).join("")}</nav></div>`;
const burger = document.querySelector(".burger"), nav = el("nav");
burger.onclick = () => burger.setAttribute("aria-expanded", nav.classList.toggle("show"));
el("site-footer").innerHTML = `<div class="wrap"><div class="fcols">
  <div><h3>${CONFIG.name}</h3><p class="lead">${CONFIG.tagline}</p></div>
  <div><h3>Visit</h3><p>${CONFIG.address}</p><a href="tel:${CONFIG.phone.replace(/\s/g,"")}">${CONFIG.phone}</a></div>
  <div><h3>Follow</h3><a href="${CONFIG.instagram}">Instagram</a><a href="${CONFIG.mapsLink}">Google Maps</a></div>
  </div><p class="copy">${CONFIG.name}, Vapi. Student project by Jacky Kumar, not the official website.</p></div>`;
/* ===== MENU ===== */
const card = (m, i) => `<article class="item">${m.img ? `<img src="images/${m.img}.jpg" alt="${m.n}" loading="lazy">` : `<div class="ph" aria-hidden="true">${m.n[0]}</div>`}
  <div><div class="top"><h3>${m.n}</h3><span class="price">${m.price ? "₹"+m.price : "₹ —"}</span></div><p>${m.d}</p><div class="call">${m.c}</div>${m.price ? `<button class="add" type="button" data-add="${MENU.indexOf(m)}">Add to order</button>` : ""}</div></article>`;
const list = el("menu-list");
if (list) {
  const cats = ["All", ...new Set(MENU.map(m => m.c))], tabs = el("tabs");
  const show = (c) => { list.innerHTML = MENU.filter(m => c==="All" || m.c===c).map(card).join("");
    [...tabs.children].forEach(b => b.setAttribute("aria-pressed", b.textContent===c)); };
  tabs.innerHTML = cats.map(c => `<button type="button">${c}</button>`).join("");
  tabs.onclick = (e) => e.target.matches("button") && show(e.target.textContent);
  show("All");
}
const featured = el("featured");
if (featured) featured.innerHTML = [0,3,5,8].map(i => card(MENU[i])).join("");
/* ===== GALLERY ===== */
const gal = el("gallery-grid"), box = el("lightbox");
if (gal) {
  gal.innerHTML = GALLERY.map(([f,t]) => `<button type="button" data-f="${f}" data-t="${t}"><img src="images/${f}.jpg" alt="${t}" loading="lazy"></button>`).join("");
  gal.onclick = (e) => { const b = e.target.closest("button"); if (!b) return;
    box.querySelector("img").src = `images/${b.dataset.f}.jpg`; box.querySelector("img").alt = b.dataset.t; box.showModal(); };
  box.querySelector("button").onclick = () => box.close();
  box.onclick = (e) => e.target === box && box.close();
}
/* ===== CONTACT ===== */
const hours = el("hours");
if (hours) hours.innerHTML = CONFIG.hours.map(([d,t]) => `<dt>${d}</dt><dd>${t}</dd>`).join("");
document.querySelectorAll("[data-cfg]").forEach(n => n.textContent = CONFIG[n.dataset.cfg]);
const map = el("map");
if (map) map.src = `https://www.google.com/maps?q=${CONFIG.lat},${CONFIG.lng}&z=17&output=embed`;
document.querySelectorAll(".directions").forEach(a => a.href = `https://www.google.com/maps/dir/?api=1&destination=${CONFIG.lat},${CONFIG.lng}`);
const form = el("msg-form");
if (form) form.onsubmit = (e) => { e.preventDefault(); const f = new FormData(form);
  const text = `Hi ${CONFIG.name}, I'm ${f.get("name")} (${f.get("phone")}). ${f.get("message")}`;
  window.open(`https://wa.me/${CONFIG.whatsapp}?text=${encodeURIComponent(text)}`, "_blank"); };

/* ===== CART, ORDER AND PAYMENT ===== */
const store = (k, v) => { try { if (v === undefined) return JSON.parse(localStorage.getItem(k) || "null"); localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} return v; };
const esc = (t) => String(t).replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
const getCart = () => store("cart") || {};
const lines = () => Object.entries(getCart()).map(([i, q]) => ({m: MENU[i], q, i})).filter(l => l.m);
const sum = () => lines().reduce((t, l) => t + l.m.price * l.q, 0);
const badge = () => { const c = el("cc"); if (c) c.textContent = lines().reduce((t, l) => t + l.q, 0); };
const toast = el("toast") || document.body.appendChild(Object.assign(document.createElement("div"), {id: "toast"}));
toast.setAttribute("role", "status");
const say = (t) => { toast.textContent = t; toast.classList.add("on"); setTimeout(() => toast.classList.remove("on"), 1800); };
const setQty = (i, d) => { const c = getCart(); c[i] = Math.max(0, (c[i] || 0) + d); if (!c[i]) delete c[i]; store("cart", c); badge(); drawCart(); };
document.addEventListener("click", (e) => {
  const a = e.target.closest("[data-add]"); if (a) { setQty(a.dataset.add, 1); say(MENU[a.dataset.add].n + " added"); }
  const q = e.target.closest("[data-q]"); if (q) setQty(...q.dataset.q.split(":").map(Number));
});
badge();
function drawCart() {
  const box = el("cart"); if (!box) return;
  const L = lines();
  box.innerHTML = L.length ? L.map(l => `<div class="crow"><span>${l.m.n}</span><span class="qty"><button type="button" aria-label="Remove one ${l.m.n}" data-q="${l.i}:-1">−</button>${l.q}<button type="button" aria-label="Add one ${l.m.n}" data-q="${l.i}:1">+</button></span><b>₹${l.m.price * l.q}</b></div>`).join("")
    : `<p class="lead">Your cart is empty. <a href="menu.html"><u>Pick something from the menu</u></a>.</p>`;
  el("total").textContent = "₹" + sum();
}
const oform = el("order-form");
const fail = (m) => { const x = el("err"); if (x) x.textContent = m; else say(m); };
if (oform) {
  drawCart();
  oform.onsubmit = async (e) => {
    e.preventDefault(); fail(""); const L = lines(); if (!L.length) return fail("Add something to your order first");
    const f = Object.fromEntries(new FormData(oform)), total = sum(), table = (f.table || "").trim();
    if (f.type === "Dine-in" && !table) return fail("Please enter your table number");
    if ((f.phone || "").replace(/\D/g, "").length < 8) return fail("Please enter a valid phone number");
    const sendBtn = oform.querySelector("button[type=submit]"); sendBtn.disabled = true; const unlock = () => { sendBtn.disabled = false; return false; };
    const o = {id: String(Date.now()).slice(-6), name: f.name, phone: f.phone, type: f.type, table, note: f.note, total, items: L.map(l => ({name: l.m.n, qty: l.q, price: l.m.price})), lines: L.map(l => `${l.q} x ${l.m.n} = ₹${l.m.price * l.q}`)};
    if (CONFIG.api) {
      let r; try { r = await fetch(CONFIG.api + "order.php", {method: "POST", headers: {"Content-Type": "application/json"}, body: JSON.stringify(o)}); }
      catch (err) { unlock(); return fail("Could not reach the server. Open the site at http://localhost/cafe-website/ (not by double-clicking the file) and check that Apache and MySQL are running."); }
      let j = {}; try { j = await r.json(); } catch (err) {}
      if (!r.ok || !j.code) { unlock(); return fail(j.error || "The server returned an error. Check that MySQL is running, database.sql is imported and api/config.php exists."); }
      o.id = j.code;
    }
    store("cart", {}); badge(); drawCart(); oform.hidden = true; el("done").hidden = false;
    const wa = `New order #${o.id}\n${o.lines.join("\n")}\nTotal: ₹${total}\n${o.type}${table ? ", table " + table : ""}\nName: ${o.name}, ${o.phone}${o.note ? "\nNote: " + o.note : ""}`;
    el("done").innerHTML = CONFIG.api
      ? `<h2>Order ${esc(o.id)} sent to the kitchen</h2><p class="lead">${table ? "Table " + esc(table) + ". " : ""}We are preparing your order. When you finish, pay the bill at the counter by UPI, cash or card.</p><div class="row"><a class="btn" href="pay.html">Pay your bill</a><a class="btn ghost" href="reviews.html">Leave a review</a></div>`
      : `<h2>Almost done</h2><p class="lead">Total ₹${total}. Send the order to the cafe on WhatsApp and pay the bill at the cafe.</p><div class="row"><a class="btn" target="_blank" rel="noopener" href="https://wa.me/${CONFIG.whatsapp}?text=${encodeURIComponent(wa)}">Send order on WhatsApp</a></div>`;
  };
}
/* ===== REVIEWS ===== */
const rl = el("review-list");
if (rl) {
  let remote = []; const load = () => CONFIG.api && fetch(CONFIG.api + "review.php").then(r => r.ok ? r.json() : []).then(d => { remote = Array.isArray(d) ? d : []; draw(); }).catch(() => {});
  const draw = () => { const R = (CONFIG.api ? remote : store("reviews") || []).slice().reverse();
    el("avg").textContent = R.length ? (R.reduce((t, r) => t + r.stars, 0) / R.length).toFixed(1) + " out of 5 (" + R.length + ")" : "No reviews yet. Be the first.";
    rl.innerHTML = R.map(r => `<article class="rev"><div class="top"><h3>${esc(r.name)}</h3><span class="stars" role="img" aria-label="${r.stars} out of 5">${"★".repeat(r.stars)}${"☆".repeat(5 - r.stars)}</span></div><p>${esc(r.text)}</p><div class="call">${esc(r.date)}</div></article>`).join(""); };
  draw(); load();
  el("review-form").onsubmit = (e) => { e.preventDefault(); const f = Object.fromEntries(new FormData(e.target));
    const r = {name: f.name.trim(), stars: +f.stars, text: f.text.trim(), date: new Date().toLocaleDateString("en-IN", {day: "numeric", month: "short", year: "numeric"})};
    if (CONFIG.reviewEndpoint) fetch(CONFIG.reviewEndpoint, {method: "POST", headers: {"Content-Type": "application/json", Accept: "application/json"}, body: JSON.stringify(r)}).catch(() => {});
    if (CONFIG.api) {
      fetch(CONFIG.api + "review.php", {method: "POST", headers: {"Content-Type": "application/json"}, body: JSON.stringify(r)})
        .then(res => { if (!res.ok) throw new Error("save failed"); e.target.reset(); say("Thanks for your review"); return load(); })
        .catch(() => say("Could not save your review. Please try again."));
      return;
    }
    store("reviews", [...(store("reviews") || []), r]); e.target.reset(); draw(); say("Thanks for your review"); };
}

/* ===== HOME REVIEWS ===== */
const hr = el("home-reviews");
if (hr) (CONFIG.api ? fetch(CONFIG.api + "review.php").then(r => r.ok ? r.json() : []) : Promise.resolve(store("reviews") || [])).then(R => {
  if (!Array.isArray(R)) R = [];
  el("home-avg").textContent = R.length ? (R.reduce((t, r) => t + r.stars, 0) / R.length).toFixed(1) + " out of 5 from " + R.length + " review" + (R.length > 1 ? "s" : "") : "No reviews yet. Be the first to write one.";
  hr.innerHTML = R.slice(-3).reverse().map(r => `<article class="rev"><div class="top"><h3>${esc(r.name)}</h3><span class="stars" role="img" aria-label="${r.stars} out of 5">${"★".repeat(r.stars)}${"☆".repeat(5 - r.stars)}</span></div><p>${esc(r.text)}</p></article>`).join("");
}).catch(() => {});

/* ===== PAY PAGE ===== */
const amt = el("amt");
if (amt) amt.oninput = () => { el("amt-out").textContent = +amt.value > 0 ? "Pay ₹" + Math.round(+amt.value) : "Enter your bill amount"; };
