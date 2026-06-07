# Product Automation Tasks

## Daily Scrape: eBay → Products

### Command
```bash
php bin/console app:scrape-ebay
```

### Location
- `src/Command/ScrapeEbayProductsCommand.php`

### What it does
Creates up to 15 products from a curated list of eBay laptop listings. Skips products with duplicate slugs.

### Cron (daily)
```cron
0 6 * * * cd /path/to/project && php bin/console app:scrape-ebay >> var/log/scrape-ebay.log 2>&1
```

### Product data
- 15 laptops across Gaming, Business, and Student categories
- Uses realistic eBay prices, specs, and SKUs
- Marks all as `newArrival` on creation

### Current products
| # | Name | Price | Category |
|---|------|-------|----------|
| 1 | Samsung Galaxy Book4 15.6" Intel Core 7 | $599.99 | Business |
| 2 | Dell Inspiron 15 3530 Touch i7-1355U | $679.00 | Business |
| 3 | HP Pavilion 15.6" AMD Ryzen 7 | $539.00 | Student |
| 4 | Lenovo IdeaPad 1 14" Intel 20GB/1TB | $279.00 | Student |
| 5 | ASUS TUF Gaming A16 Ryzen 7 RX 7700S | $849.99 | Gaming |
| 6 | Acer Nitro V 15 Ryzen 5 7535HS | $687.27 | Gaming |
| 7 | Lenovo Legion 7i 16" i7 14th Gen | $1,299.99 | Gaming |
| 8 | Apple MacBook Air M3 13.6" 2024 | $879.00 | Business |
| 9 | ASUS Vivobook S15 OLED Snapdragon X Elite | $633.00 | Business |
| 10 | Acer Predator Helios 14" Ultra 7 | $1,000.00 | Gaming |
| 11 | Lenovo IdeaPad Slim 3 Ryzen 7 7730U | $393.87 | Student |
| 12 | HP Victus Gaming 15.6" Ryzen 5 | $749.99 | Gaming |
| 13 | Dell Latitude 14" Core i7 11th Gen | $279.99 | Business |
| 14 | Microsoft Surface Laptop 7 Touch | $599.00 | Business |
| 15 | Lenovo ThinkPad E495 Ryzen 5 | $199.99 | Business |

## Like System

### Toggle Like
- **Route**: `POST /products/{id}/like` — name: `product_like`
- **Controller**: `ProductController::like()`
- **Auth**: Requires `ROLE_USER`
- **Entity**: `ProductLike` (user_id, product_id, created_at) — unique constraint per user+product
- **JS**: Click handler on `[data-like]` buttons sends POST, updates icon class and `likesCount` display

### View Wishlist
- **Route**: `GET /products/wishlist` — name: `product_wishlist`
- **Controller**: `ProductController::wishlist()`
- **Auth**: Requires `ROLE_USER`
- **Template**: `templates/product/wishlist.html.twig`
- **Navbar icon**: `favorite` visible for authenticated users next to cart icon

## Cart System

### Add to Cart
- **Route**: `POST /cart/add/{id}` — name: `cart_add`
- **Controller**: `CartController::add()`
- **Auth**: Requires `ROLE_USER`
- **Template button**: ProductCard overlay has `shopping_cart` icon with item count badge

### View Cart
- **Route**: `GET /cart` — name: `cart_index`
- **Template**: `templates/cart/index.html.twig`

## Comparison System

### Add to Compare
- **Route**: `POST /compare/add/{id}` — name: `comparison_add`
- **Controller**: `ComparisonController::add()`
- **Limit**: 4 products max

### View Comparison
- **Route**: `GET /compare` — name: `comparison_index`
- **Template**: `templates/comparison/index.html.twig`
- **Navbar icon**: `compare_arrows` always visible

## Checkout Flow

### Proceed to Checkout
- **Route**: `POST /order/checkout` — name: `order_checkout`
- **Controller**: `OrderController::checkout()`
- **Auth**: Requires `ROLE_USER`
- **Action**: Creates order, clears cart, redirects to `admin_dashboard` with success flash

## Admin Product CRUD

### Dashboard
- **Route**: `GET /admin` — name: `admin_dashboard`
- **Stats**: Total products, categories, orders, revenue, active deals, low stock

### Product List
- **Route**: `GET /admin/products` — name: `admin_product_index`

### Product Create
- **Route**: `GET/POST /admin/products/new` — name: `admin_product_new`

### Product Edit
- **Route**: `GET/POST /admin/products/{id}/edit` — name: `admin_product_edit`
- **Form fields**: name, sku, shortDescription, description, price, salePrice, stock, categories, isFeatured, isNewArrival, imageUrl

### Product Delete
- **Route**: `POST /admin/products/{id}/delete` — name: `admin_product_delete`

## Admin Order Management

### Order List
- **Route**: `GET /admin/orders` — name: `admin_order_index`

### Order Detail
- **Route**: `GET /admin/orders/{id}` — name: `admin_order_show`
