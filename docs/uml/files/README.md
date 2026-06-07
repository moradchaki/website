# E-Commerce Daily Site Generator — Skill

Automate the creation of a daily e-commerce page with products, pricing, countdown timer, and CTAs.

## Quick Start

### 1. Add Your Products

Edit `products.json`:
```json
{
  "products": [
    {
      "id": "p001",
      "name": "My Product",
      "description": "Short description here.",
      "originalPrice": "49.99",
      "salePrice": "24.99",
      "badge": "50% OFF",
      "image": "https://example.com/image.jpg"
    }
  ]
}
```

### 2. Configure the Generator

Edit the config block at the top of `scripts/generate.sh`:
```bash
STORE_NAME="My Store"
DEAL_TYPE="Flash Sale"
ACCENT_COLOR="#e53e3e"
# ... etc
```

Or for Python, edit `scripts/generate.py`:
```python
CONFIG = {
    "store_name": "My Store",
    ...
}
```

### 3. Run It

**Bash (simple):**
```bash
chmod +x scripts/generate.sh
./scripts/generate.sh
```

**Python (advanced, supports CSV):**
```bash
pip install jinja2
python scripts/generate.py --products products.json --output dist/
```

Output: `dist/YYYY-MM-DD/index.html`

---

## Automate with Cron

Run daily at midnight:
```bash
crontab -e
```
Add:
```
1 0 * * * /full/path/to/scripts/generate.sh >> /var/log/ecommerce.log 2>&1
```

---

## File Structure

```
ecommerce-daily-site/
├── SKILL.md                         # Skill definition for Claude Code
├── README.md                        # This file
├── products.json                    # Your product data
├── scripts/
│   ├── generate.sh                  # Bash automation script
│   └── generate.py                  # Python automation script
├── references/
│   ├── html-template.md             # Full annotated HTML template
│   ├── products-schema.md           # JSON/CSV schema reference
│   └── styling-guide.md             # Colors, palettes, card variants
└── dist/
    └── YYYY-MM-DD/
        ├── index.html               # Generated daily page
        └── products.json            # Products snapshot for that day
```

---

## Deploying

### Via rsync to a VPS
Uncomment the deploy section in `generate.sh` and set:
```bash
REMOTE_USER="ubuntu"
REMOTE_HOST="your-server.com"
REMOTE_PATH="/var/www/html"
```

### Via GitHub Pages
Push `dist/` to a `gh-pages` branch and enable GitHub Pages in your repo settings.

### Via Netlify / Vercel
Point your site root to `dist/YYYY-MM-DD/` and trigger a deploy via their CLI after generation.

---

## Customization

- **Colors**: See `references/styling-guide.md` for pre-built palettes
- **Card styles**: Featured, horizontal, and minimal card variants available
- **Countdown**: Set `saleEndHour` in the config (default: 23 = end of day)
- **Badges**: Per-product or global default badge text
