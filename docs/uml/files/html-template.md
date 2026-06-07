# Base HTML Template — Annotated

Complete starter template for the daily e-commerce page.
Replace all `{{VARIABLE}}` placeholders via the generate script or manually.

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{STORE_NAME}} — {{DEAL_TYPE}} — {{DATE}}">
  <title>{{STORE_NAME}} | {{DEAL_TYPE}}</title>
  <style>
    /* ── Design Tokens ── */
    :root {
      --accent:       {{ACCENT_COLOR}};   /* e.g. #e53e3e */
      --accent-dark:  {{ACCENT_DARK}};    /* e.g. #c53030 */
      --bg:           #f9fafb;
      --surface:      #ffffff;
      --text:         #111827;
      --muted:        #6b7280;
      --border:       #e5e7eb;
      --radius:       12px;
      --shadow:       0 2px 12px rgba(0,0,0,.08);
      --font:         system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    }

    /* ── Reset ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    img { display: block; max-width: 100%; height: auto; }
    body { font-family: var(--font); background: var(--bg); color: var(--text); }

    /* ── Header ── */
    .site-header {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky; top: 0; z-index: 100;
    }
    .site-logo { font-size: 1.4rem; font-weight: 800; color: var(--accent); }
    .cart-icon { cursor: pointer; font-size: 1.5rem; position: relative; }
    .cart-count {
      position: absolute; top: -6px; right: -8px;
      background: var(--accent); color: white;
      border-radius: 50%; width: 18px; height: 18px;
      font-size: .7rem; display: flex; align-items: center; justify-content: center;
    }

    /* ── Hero ── */
    .hero {
      background: linear-gradient(135deg, var(--accent), var(--accent-dark));
      color: white;
      text-align: center;
      padding: 4rem 1.5rem;
    }
    .hero-eyebrow { font-size: .875rem; letter-spacing: .1em; text-transform: uppercase; opacity: .85; }
    .hero-title   { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; margin: .5rem 0; }
    .hero-date    { font-size: 1rem; opacity: .9; margin-bottom: 1.5rem; }
    .hero-cta     {
      display: inline-block; padding: .85rem 2.5rem;
      background: white; color: var(--accent);
      font-weight: 700; font-size: 1.05rem;
      border-radius: 9999px; text-decoration: none;
      transition: transform .15s, box-shadow .15s;
    }
    .hero-cta:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.2); }

    /* ── Countdown ── */
    .countdown-bar {
      background: var(--text); color: white;
      text-align: center; padding: .6rem;
      font-size: .875rem;
    }
    .countdown-bar span { font-family: monospace; font-size: 1.1rem; font-weight: 700; }

    /* ── Products ── */
    .products-section { padding: 3rem 1.5rem; max-width: 1200px; margin: 0 auto; }
    .section-title    { font-size: 1.75rem; font-weight: 800; margin-bottom: 1.5rem; }
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 1.5rem;
    }

    /* ── Product Card ── */
    .product-card {
      background: var(--surface);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      position: relative;
      transition: transform .2s, box-shadow .2s;
    }
    .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
    .product-card img   { width: 100%; height: 200px; object-fit: cover; }

    .badge {
      position: absolute; top: .75rem; left: .75rem;
      background: var(--accent); color: white;
      font-size: .7rem; font-weight: 700;
      padding: .2rem .6rem; border-radius: 4px;
      text-transform: uppercase; letter-spacing: .05em;
    }

    .card-body    { padding: 1rem; }
    .product-name { font-size: 1rem; font-weight: 700; margin-bottom: .35rem; }
    .product-desc { font-size: .85rem; color: var(--muted); margin-bottom: .75rem; line-height: 1.4; }

    .pricing        { display: flex; align-items: center; gap: .5rem; margin-bottom: 1rem; }
    .price-original { text-decoration: line-through; color: var(--muted); font-size: .875rem; }
    .price-sale     { font-size: 1.3rem; font-weight: 800; color: var(--accent); }

    .btn-cta {
      width: 100%; padding: .65rem;
      background: var(--accent); color: white;
      border: none; border-radius: 8px;
      font-size: .95rem; font-weight: 700; cursor: pointer;
      transition: background .15s, transform .1s;
    }
    .btn-cta:hover   { background: var(--accent-dark); }
    .btn-cta:active  { transform: scale(.97); }
    .btn-cta.added   { background: #16a34a; }

    /* ── Footer ── */
    .site-footer {
      background: var(--text); color: #d1d5db;
      text-align: center; padding: 2rem 1.5rem;
      font-size: .875rem; margin-top: 4rem;
    }
    .site-footer a { color: var(--accent); text-decoration: none; }

    /* ── Responsive ── */
    @media (max-width: 640px) {
      .products-grid { grid-template-columns: 1fr 1fr; gap: 1rem; }
      .hero { padding: 2.5rem 1rem; }
    }
    @media (max-width: 400px) {
      .products-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- HEADER -->
<header class="site-header">
  <div class="site-logo">{{STORE_NAME}}</div>
  <div class="cart-icon" onclick="openCart()">
    🛒 <span class="cart-count" id="cart-count">0</span>
  </div>
</header>

<!-- COUNTDOWN BAR -->
<div class="countdown-bar">
  ⏰ {{DEAL_TYPE}} ends in: <span id="countdown">--:--:--</span>
</div>

<!-- HERO -->
<section class="hero">
  <p class="hero-eyebrow">{{DEAL_TYPE}}</p>
  <h1 class="hero-title">{{HERO_HEADING}}</h1>
  <p class="hero-date js-date"></p>
  <a href="#products" class="hero-cta">{{CTA_LABEL}}</a>
</section>

<!-- PRODUCTS -->
<section class="products-section" id="products">
  <h2 class="section-title">{{SECTION_TITLE}}</h2>
  <div class="products-grid" id="products-grid">
    <!-- JS will inject product cards here from products.json -->
  </div>
</section>

<!-- FOOTER -->
<footer class="site-footer">
  <p>&copy; <span class="js-year"></span> {{STORE_NAME}}. All rights reserved.</p>
  <p style="margin-top:.5rem">{{CONTACT_INFO}}</p>
</footer>

<script>
  // ── Config ──────────────────────────────────────────────
  const CONFIG = {
    accentColor:   '{{ACCENT_COLOR}}',
    accentDark:    '{{ACCENT_DARK}}',
    ctaLabel:      '{{CTA_LABEL}}',
    badgeText:     '{{BADGE_TEXT}}',
    currency:      '{{CURRENCY_SYMBOL}}',
    productsFile:  'products.json',
    saleEndHour:   23,
  };

  // ── Date injection ───────────────────────────────────────
  const now = new Date();
  const dateStr = now.toLocaleDateString('en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
  document.querySelectorAll('.js-date').forEach(el => el.textContent = dateStr);
  document.querySelectorAll('.js-year').forEach(el => el.textContent = now.getFullYear());

  // ── Countdown ────────────────────────────────────────────
  function startCountdown(endHour = 23) {
    const tick = () => {
      const n = new Date(), e = new Date();
      e.setHours(endHour, 59, 59, 0);
      const diff = e - n;
      if (diff <= 0) { document.getElementById('countdown').textContent = 'Offer Ended'; return; }
      const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
      const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
      const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
      document.getElementById('countdown').textContent = `${h}:${m}:${s}`;
      setTimeout(tick, 1000);
    };
    tick();
  }
  startCountdown(CONFIG.saleEndHour);

  // ── Cart ─────────────────────────────────────────────────
  let cart = [];
  function addToCart(id) {
    cart.push(id);
    document.getElementById('cart-count').textContent = cart.length;
    const btn = document.querySelector(`[data-id="${id}"]`);
    if (btn) { btn.textContent = '✓ Added'; btn.classList.add('added'); }
  }
  function openCart() {
    alert(`Cart: ${cart.length} item(s). Checkout coming soon!`);
  }

  // ── Product rendering ─────────────────────────────────────
  function renderProducts(products) {
    const grid = document.getElementById('products-grid');
    grid.innerHTML = products.map(p => `
      <article class="product-card">
        <div class="badge">${p.badge || CONFIG.badgeText}</div>
        <img src="${p.image || 'https://placehold.co/400x300/eee/999?text=' + encodeURIComponent(p.name)}"
             alt="${p.name}" loading="lazy">
        <div class="card-body">
          <h3 class="product-name">${p.name}</h3>
          <p class="product-desc">${p.description}</p>
          <div class="pricing">
            <span class="price-original">${CONFIG.currency}${p.originalPrice}</span>
            <span class="price-sale">${CONFIG.currency}${p.salePrice}</span>
          </div>
          <button class="btn-cta" data-id="${p.id}" onclick="addToCart('${p.id}')">
            ${CONFIG.ctaLabel}
          </button>
        </div>
      </article>
    `).join('');
  }

  // ── Load products (fetch JSON or inline fallback) ─────────
  fetch('products.json')
    .then(r => r.ok ? r.json() : Promise.reject())
    .then(data => renderProducts(data.products || data))
    .catch(() => {
      // Inline fallback products
      renderProducts(INLINE_PRODUCTS);
    });

  // ── Inline fallback (used when products.json is missing) ──
  const INLINE_PRODUCTS = [
    { id:'p1', name:'Sample Product 1', description:'Great product description here.', originalPrice:'49.99', salePrice:'24.99', image:'', badge:'50% OFF' },
    { id:'p2', name:'Sample Product 2', description:'Another great product for your customers.', originalPrice:'89.99', salePrice:'59.99', image:'', badge:'HOT' },
    { id:'p3', name:'Sample Product 3', description:'Limited quantity — don\'t miss out.', originalPrice:'34.99', salePrice:'19.99', image:'', badge:'LIMITED' },
  ];
</script>
</body>
</html>
```

## Variable Reference

| Variable | Description | Example |
|---|---|---|
| `{{STORE_NAME}}` | Brand / store name | TechDrop |
| `{{DEAL_TYPE}}` | Type of daily offer | Flash Sale |
| `{{HERO_HEADING}}` | Main hero text | Today's Hottest Deals |
| `{{CTA_LABEL}}` | Button text | Shop Now |
| `{{SECTION_TITLE}}` | Products section heading | Today's Picks |
| `{{ACCENT_COLOR}}` | Primary hex color | #e53e3e |
| `{{ACCENT_DARK}}` | Darker shade for hover | #c53030 |
| `{{CURRENCY_SYMBOL}}` | Currency symbol | $ |
| `{{BADGE_TEXT}}` | Default badge label | TODAY ONLY |
| `{{CONTACT_INFO}}` | Footer contact | contact@store.com |
