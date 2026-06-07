# Products Data Schema

## JSON Format (`products.json`)

```json
{
  "store": "TechDrop",
  "dealType": "Flash Sale",
  "date": "auto",
  "products": [
    {
      "id": "p001",
      "name": "Wireless Noise-Cancelling Headphones",
      "description": "40hr battery, premium sound, foldable design.",
      "originalPrice": "149.99",
      "salePrice": "79.99",
      "badge": "47% OFF",
      "image": "https://placehold.co/400x300/1a202c/white?text=Headphones",
      "stock": 12,
      "tags": ["electronics", "audio"]
    },
    {
      "id": "p002",
      "name": "Smart Watch Pro",
      "description": "Health tracking, GPS, 5-day battery life.",
      "originalPrice": "299.99",
      "salePrice": "189.99",
      "badge": "TODAY ONLY",
      "image": "https://placehold.co/400x300/2d3748/white?text=SmartWatch",
      "stock": 5,
      "tags": ["wearable", "electronics"]
    }
  ]
}
```

## Field Definitions

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | string | ✅ | Unique identifier, used for cart tracking |
| `name` | string | ✅ | Product display name (max 60 chars) |
| `description` | string | ✅ | Short description (max 120 chars) |
| `originalPrice` | string | ✅ | Before-sale price (no currency symbol) |
| `salePrice` | string | ✅ | Today's price (no currency symbol) |
| `badge` | string | optional | Override default badge text (e.g. "HOT", "NEW") |
| `image` | string | optional | URL; falls back to placeholder if empty |
| `stock` | number | optional | Used for "X left" urgency text |
| `tags` | string[] | optional | For filtering/categorization |

## CSV Format (alternative input)

When users provide product data as CSV, convert to JSON before generating the page.

```csv
id,name,description,originalPrice,salePrice,badge,image
p001,Wireless Headphones,40hr battery premium sound,149.99,79.99,47% OFF,
p002,Smart Watch Pro,Health tracking GPS 5-day battery,299.99,189.99,TODAY ONLY,
p003,Portable Speaker,Waterproof 360° sound 20hr battery,79.99,39.99,50% OFF,
```

## Python snippet to convert CSV → JSON

```python
import csv, json, sys

with open(sys.argv[1]) as f:
    reader = csv.DictReader(f)
    products = [row for row in reader]

with open('products.json', 'w') as f:
    json.dump({"products": products}, f, indent=2)

print(f"Converted {len(products)} products to products.json")
```

## Pricing Display Rules

- Always show `originalPrice` with strikethrough
- `salePrice` in accent color, larger font
- Auto-calculate discount % if not provided as badge:
  ```javascript
  const pct = Math.round((1 - salePrice/originalPrice) * 100);
  badge = badge || `${pct}% OFF`;
  ```

## Stock Urgency Logic

If `stock` field is present and stock ≤ 5, append urgency text to description:
```
"Only 3 left in stock!"
```
