#!/usr/bin/env python3
"""
generate.py — Daily E-Commerce Page Generator (Python / Jinja2 version)
Supports larger product catalogs with filtering, sorting, and categories.

Requirements: pip install jinja2
Usage: python generate.py [--products products.json] [--output dist/]
"""

import json
import argparse
import shutil
from pathlib import Path
from datetime import datetime

try:
    from jinja2 import Environment, FileSystemLoader
    HAS_JINJA2 = True
except ImportError:
    HAS_JINJA2 = False

# ── Config ────────────────────────────────────────────────────
CONFIG = {
    "store_name":      "My Store",
    "deal_type":       "Flash Sale",
    "hero_heading":    "Today's Hottest Deals",
    "cta_label":       "Shop Now",
    "section_title":   "Today's Picks",
    "accent_color":    "#e53e3e",
    "accent_dark":     "#c53030",
    "currency_symbol": "$",
    "badge_text":      "TODAY ONLY",
    "contact_info":    "contact@mystore.com",
}


def load_products(products_file: Path) -> list:
    """Load products from JSON or CSV."""
    if not products_file.exists():
        print(f"⚠️  {products_file} not found — using sample products")
        return get_sample_products()

    if products_file.suffix == '.json':
        with open(products_file) as f:
            data = json.load(f)
        return data.get('products', data) if isinstance(data, dict) else data

    if products_file.suffix == '.csv':
        import csv
        with open(products_file) as f:
            return list(csv.DictReader(f))

    raise ValueError(f"Unsupported products format: {products_file.suffix}")


def get_sample_products() -> list:
    return [
        {
            "id": "p001",
            "name": "Sample Product 1",
            "description": "Great product — limited time offer.",
            "originalPrice": "49.99",
            "salePrice": "24.99",
            "badge": "50% OFF",
            "image": "",
            "stock": 10,
        },
        {
            "id": "p002",
            "name": "Sample Product 2",
            "description": "Another great deal for your customers.",
            "originalPrice": "89.99",
            "salePrice": "59.99",
            "badge": "HOT",
            "image": "",
            "stock": 3,
        },
        {
            "id": "p003",
            "name": "Sample Product 3",
            "description": "Don't miss this one — selling fast!",
            "originalPrice": "34.99",
            "salePrice": "19.99",
            "badge": "LIMITED",
            "image": "",
            "stock": 1,
        },
    ]


def enrich_products(products: list, config: dict) -> list:
    """Add computed fields to each product."""
    enriched = []
    for p in products:
        p = dict(p)
        # Auto-calculate discount badge if missing
        if not p.get('badge'):
            try:
                orig  = float(p['originalPrice'])
                sale  = float(p['salePrice'])
                pct   = round((1 - sale / orig) * 100)
                p['badge'] = f"{pct}% OFF"
            except (ValueError, ZeroDivisionError):
                p['badge'] = config['badge_text']

        # Add urgency text for low stock
        stock = int(p.get('stock', 999))
        if stock <= 5:
            p['urgency'] = f"Only {stock} left in stock!"
        else:
            p['urgency'] = None

        # Placeholder image if missing
        if not p.get('image'):
            label = p['name'].replace(' ', '+')
            p['image'] = f"https://placehold.co/400x300/eee/999?text={label}"

        enriched.append(p)
    return enriched


def generate_html_simple(products: list, config: dict, date_str: str) -> str:
    """Generate HTML using simple string replacement (no Jinja2 required)."""
    product_cards = ""
    for p in products:
        urgency_html = f'<p class="urgency">⚡ {p["urgency"]}</p>' if p.get('urgency') else ''
        product_cards += f"""
        <article class="product-card">
          <div class="badge">{p['badge']}</div>
          <img src="{p['image']}" alt="{p['name']}" loading="lazy">
          <div class="card-body">
            <h3 class="product-name">{p['name']}</h3>
            <p class="product-desc">{p['description']}</p>
            {urgency_html}
            <div class="pricing">
              <span class="price-original">{config['currency_symbol']}{p['originalPrice']}</span>
              <span class="price-sale">{config['currency_symbol']}{p['salePrice']}</span>
            </div>
            <button class="btn-cta" data-id="{p['id']}" onclick="addToCart('{p['id']}')">
              {config['cta_label']}
            </button>
          </div>
        </article>
        """

    # Read base template and substitute
    template_path = Path(__file__).parent.parent / "assets" / "template" / "template.html"
    if template_path.exists():
        with open(template_path) as f:
            html = f.read()
    else:
        print("⚠️  template.html not found — generating minimal page")
        html = "<html><body>{{PRODUCTS}}</body></html>"

    replacements = {
        "{{STORE_NAME}}":      config['store_name'],
        "{{DEAL_TYPE}}":       config['deal_type'],
        "{{HERO_HEADING}}":    config['hero_heading'],
        "{{CTA_LABEL}}":       config['cta_label'],
        "{{SECTION_TITLE}}":   config['section_title'],
        "{{ACCENT_COLOR}}":    config['accent_color'],
        "{{ACCENT_DARK}}":     config['accent_dark'],
        "{{CURRENCY_SYMBOL}}": config['currency_symbol'],
        "{{BADGE_TEXT}}":      config['badge_text'],
        "{{CONTACT_INFO}}":    config['contact_info'],
        "{{PRODUCTS}}":        product_cards,
    }
    for key, val in replacements.items():
        html = html.replace(key, val)

    return html


def main():
    parser = argparse.ArgumentParser(description="Generate daily e-commerce page")
    parser.add_argument('--products', default='products.json', help='Products JSON or CSV file')
    parser.add_argument('--output',   default='dist',          help='Output directory')
    parser.add_argument('--config',   default=None,            help='Config JSON file (overrides defaults)')
    args = parser.parse_args()

    # Merge config from file if provided
    config = dict(CONFIG)
    if args.config:
        with open(args.config) as f:
            config.update(json.load(f))

    today    = datetime.now()
    date_str = today.strftime('%Y-%m-%d')

    # Output path: dist/YYYY-MM-DD/
    output_dir = Path(args.output) / date_str
    output_dir.mkdir(parents=True, exist_ok=True)

    print(f"──────────────────────────────────")
    print(f"🛒  Generating e-commerce page")
    print(f"    Date:  {date_str}")
    print(f"    Store: {config['store_name']}")
    print(f"    Deal:  {config['deal_type']}")
    print(f"──────────────────────────────────")

    # Load and enrich products
    products_path = Path(args.products)
    raw_products  = load_products(products_path)
    products      = enrich_products(raw_products, config)
    print(f"✅ Loaded {len(products)} products")

    # Generate HTML
    html = generate_html_simple(products, config, date_str)

    # Write output
    output_file = output_dir / "index.html"
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(html)
    print(f"✅ Generated: {output_file}")

    # Copy products.json to output
    if products_path.exists():
        shutil.copy(products_path, output_dir / "products.json")
        print(f"✅ Copied: {output_dir}/products.json")

    print(f"\nDone! Open {output_file} in your browser.")


if __name__ == '__main__':
    main()
