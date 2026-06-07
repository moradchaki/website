---
name: E-Commerce Daily Site Generator
description: This skill should be used when the user asks to "create a daily e-commerce website", "generate a shop page for today", "automate my daily store", "build a product landing page", "create a daily deal site", "generate a product catalog page", "build a flash sale page", "make a daily offer website", "automate e-commerce page creation", or any request to generate a recurring or scheduled e-commerce web page. Use when the user wants a templated, repeatable workflow for producing e-commerce HTML pages with products, pricing, CTAs, and dynamic daily content.
version: 1.0.0
---

# E-Commerce Daily Site Generator

Automate the creation of professional, ready-to-publish e-commerce HTML pages on a daily cadence. This skill produces complete, self-contained HTML files with products, pricing, offers, and CTAs — no framework required.

## When to Use This Skill

Trigger when the user wants to:
- Generate a daily product landing page or flash-sale site
- Automate the repetitive task of building shop/catalog pages
- Produce a complete HTML e-commerce page from product data (JSON, CSV, or plain input)
- Build a scheduled or templated storefront

---

## Core Workflow

### Step 1 — Gather Input

Collect the minimum required information before generating:

| Field | Required | Example |
|---|---|---|
| Store name | ✅ | "TechDrop" |
| Daily theme / deal type | ✅ | "Flash Sale", "New Arrivals", "Weekend Deals" |
| Product list | ✅ | Name, price, image URL or placeholder, short description |
| Currency | ✅ | USD, EUR, MAD… |
| Primary CTA label | ✅ | "Buy Now", "Shop Today", "Grab the Deal" |
| Accent color (hex) | optional | `#e53e3e` (default: deep red) |
| Discount badge text | optional | "50% OFF", "Today Only" |
| Footer / contact info | optional | Email, social links |

If product data is missing, generate 3–6 realistic placeholder products relevant to the theme.

---

### Step 2 — Build the Page

Generate a **single self-contained HTML file**. Follow these rules:

#### Structure
```
index.html
├── <head>       — Meta, title, inline CSS
├── <header>     — Logo + store name + tagline
├── <hero>       — Daily offer banner with CTA
├── <products>   — Product grid (CSS Grid, 2–4 cols)
├── <footer>     — Contact, copyright, date stamp
└── <script>     — Countdown timer (optional), cart feedback
```

#### Design Rules
- Use **CSS custom properties** (`--accent`, `--bg`, `--text`) for theming
- Mobile-first responsive layout (single column on < 640px)
- No external dependencies — embed all styles and scripts inline
- Product cards: image, name, price (strike-through original + sale), badge, CTA button
- Hero section: large heading, subheading with date, primary CTA button
- Color palette: white base, accent for CTAs and badges, dark text
- Font: system-ui stack (no Google Fonts unless user requests)

#### Dynamic Date Injection
Always inject today's date into the page title, hero heading, and `<time>` elements using JS:
```javascript
const today = new Date().toLocaleDateString('en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
document.querySelectorAll('.js-date').forEach(el => el.textContent = today);
```

#### Countdown Timer (include by default for flash sales)
```javascript
function startCountdown(endHour = 23) {
  const tick = () => {
    const now = new Date();
    const end = new Date(); end.setHours(endHour, 59, 59, 0);
    const diff = end - now;
    if (diff <= 0) { document.getElementById('countdown').textContent = 'Offer Ended'; return; }
    const h = String(Math.floor(diff/3600000)).padStart(2,'0');
    const m = String(Math.floor((diff%3600000)/60000)).padStart(2,'0');
    const s = String(Math.floor((diff%60000)/1000)).padStart(2,'0');
    document.getElementById('countdown').textContent = `${h}:${m}:${s}`;
    setTimeout(tick, 1000);
  };
  tick();
}
startCountdown();
```

---

### Step 3 — Product Card Template

Repeat this pattern for each product:

```html
<article class="product-card">
  <div class="badge">TODAY ONLY</div>
  <img src="IMAGE_URL" alt="PRODUCT_NAME" loading="lazy">
  <div class="card-body">
    <h3 class="product-name">PRODUCT_NAME</h3>
    <p class="product-desc">SHORT_DESCRIPTION</p>
    <div class="pricing">
      <span class="price-original">$ORIGINAL</span>
      <span class="price-sale">$SALE_PRICE</span>
    </div>
    <button class="btn-cta" onclick="addToCart('PRODUCT_ID')">CTA_LABEL</button>
  </div>
</article>
```

---

### Step 4 — Automation Script

After generating the HTML, also produce `generate.sh` (see `scripts/generate.sh`) — a Bash script the user can run daily via cron to regenerate the page automatically.

The script:
1. Reads products from `products.json`
2. Calls a template engine (Python `string.Template` or `sed`) to inject product data
3. Writes a dated output file: `dist/YYYY-MM-DD/index.html`
4. Optionally runs `rsync` or `scp` to deploy to a server

---

### Step 5 — Output Checklist

Before delivering the files, verify:

- [ ] HTML validates (no unclosed tags)
- [ ] All `--css-vars` referenced are defined in `:root`
- [ ] Mobile layout works (test with `max-width: 375px` mental model)
- [ ] Date injects correctly on load
- [ ] Countdown timer present for flash-sale pages
- [ ] `generate.sh` is executable and references correct paths
- [ ] All placeholder images use a valid fallback (e.g. `https://placehold.co/400x300`)

---

## File Delivery

Always deliver:

| File | Purpose |
|---|---|
| `index.html` | The complete daily e-commerce page |
| `products.json` | Product data source for automation |
| `generate.sh` | Daily automation script |
| `README.md` | Setup and cron instructions |

---

## Cron Setup (include in README)

```bash
# Run every day at 00:01 to regenerate the page
1 0 * * * /path/to/generate.sh >> /var/log/ecommerce-gen.log 2>&1
```

---

## Additional Resources

- **`references/html-template.md`** — Full annotated base HTML template
- **`references/products-schema.md`** — JSON schema and CSV format for product input
- **`references/styling-guide.md`** — CSS variables, color palettes, card variants
- **`scripts/generate.sh`** — Bash automation script for daily regeneration
- **`scripts/generate.py`** — Python version with Jinja2 templating (for larger catalogs)
