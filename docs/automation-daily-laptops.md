# Daily Laptop Automation — 15 Products Every Day

## Command

```bash
php bin/console app:auto-generate-laptops
```

## Location

- `src/Command/AutoGenerateLaptopsCommand.php`

## What it does

Creates **15 laptop products** per run from a pool of realistic laptop names, brands, specs, and prices. Each day generates unique product names by combining random brands, models, and suffixes so slugs rarely collide. Products that already exist (duplicate slug) are skipped.

- **Categories used**: Gaming, Business, Student (5 per category)
- **Brands**: Apple, ASUS, Acer, Dell, HP, Lenovo, Microsoft, Samsung, MSI, Razer, Gigabyte, Huawei
- **Images**: Unsplash laptop photos (random from 12 curated URLs)
- **Marked as**: `newArrival`
- **Stock**: Random 5–25

## Cron (daily at 6 AM)

```cron
0 6 * * * cd /path/to/project && php bin/console app:auto-generate-laptops >> var/log/auto-laptops.log 2>&1
```

## Scheduling via Windows Task Scheduler (dev machine)

```powershell
$action = New-ScheduledTaskAction -Execute "php.exe" -Argument "bin/console app:auto-generate-laptops" -WorkingDirectory "C:\path\to\project"
$trigger = New-ScheduledTaskTrigger -Daily -At 06:00
Register-ScheduledTask -TaskName "LaptopsDailyGenerate" -Action $action -Trigger $trigger
```

## How it works

| Step | Detail |
|------|--------|
| 1 | Picks 15 brand+model combinations from 48 name pools |
| 2 | Generates slug from lowercased name |
| 3 | Skips if slug already exists in database |
| 4 | Assigns Gaming/Business/Student category (5 each) |
| 5 | Sets random price $199–$2,499, stock 5–25 |
| 6 | Assigns random Unsplash laptop image |
| 7 | Generates realistic spec string (CPU, RAM, storage, display) |

## Monitoring

Check the log for results:

```bash
cat var/log/auto-laptops.log
```

Example output:

```
[2026-06-06 06:00:01] App\Command\AutoGenerateLaptopsCommand: Created 15 laptop products (0 skipped, 0 failed)
[2026-06-07 06:00:01] App\Command\AutoGenerateLaptopsCommand: Created 13 laptop products (2 skipped — duplicate slugs)
```
