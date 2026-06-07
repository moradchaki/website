# Daily Order Automation

## Command

```bash
php bin/console app:auto-generate-orders
```

## Location

- `src/Command/AutoGenerateOrdersCommand.php`

## What it does

Creates **15–20 orders per run** with random users, products, and realistic order data. Each order contains 1–4 random laptop products with calculated subtotals, tax, and totals.

- **Users**: Random existing users (skips if no users exist)
- **Products**: Random existing laptop products (skips if none exist)
- **Items per order**: 1–4 (random quantity 1–2 each)
- **Status distribution**: ~30% pending, ~25% confirmed, ~20% processing, ~15% shipped, ~10% delivered
- **Payment methods**: Credit Card, PayPal, Apple Pay, Google Pay, Bank Transfer
- **Shipping**: $0 (orders over $500) or $9.99
- **Tax**: 8%

## Cron (daily at 7 AM)

```cron
0 7 * * * cd /path/to/project && php bin/console app:auto-generate-orders >> var/log/auto-orders.log 2>&1
```

## Scheduling via Windows Task Scheduler

```powershell
$action = New-ScheduledTaskAction -Execute "php.exe" -Argument "bin/console app:auto-generate-orders" -WorkingDirectory "C:\path\to\project"
$trigger = New-ScheduledTaskTrigger -Daily -At 07:00
Register-ScheduledTask -TaskName "OrdersDailyGenerate" -Action $action -Trigger $trigger
```

## How it works

| Step | Detail |
|------|--------|
| 1 | Fetches all existing users and products from DB |
| 2 | Picks 15–20 random users (with replacement) |
| 3 | For each order, picks 1–4 random products |
| 4 | Generates unique order number `ORD-YYYYMMDD-NNN` |
| 5 | Calculates subtotal, tax (8%), shipping, total |
| 6 | Assigns random status and payment method |
| 7 | Creates fake shipping address from name pools |
| 8 | Flushes all orders in a single transaction |

## Order Number Format

```
ORD-20260607-001
ORD-20260607-002
...
```

Resets counter each day to avoid collisions.

## Monitoring

```bash
cat var/log/auto-orders.log
```

Example output:

```
[2026-06-07 07:00:01] App\Command\AutoGenerateOrdersCommand: Created 17 orders ($28,493.47 total revenue)
[2026-06-08 07:00:01] App\Command\AutoGenerateOrdersCommand: Created 19 orders ($31,205.83 total revenue)
```
