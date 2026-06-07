<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[AsCommand(name: 'app:auto-generate-laptops', description: 'Generate 15 laptop products daily from brand/model pools')]
class AutoGenerateLaptopsCommand extends Command
{
    private const IMAGES = [
        'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600',
        'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=600',
        'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600',
        'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=600',
        'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=600',
        'https://images.unsplash.com/photo-1530893609608-32a9af3aa95c?w=600',
        'https://images.unsplash.com/photo-1629131726692-1accd0c53ce0?w=600',
        'https://images.unsplash.com/photo-1611078489935-0cb964de46d6?w=600',
        'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=600',
        'https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=600',
        'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=600',
        'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600',
    ];

    private const BRANDS = ['ASUS', 'Acer', 'Dell', 'HP', 'Lenovo', 'MSI', 'Razer', 'Gigabyte', 'Huawei', 'Samsung'];

    private const GAMING_MODELS = [
        ['Pro GX', 'Strix G', 'TUF Dash', 'ROG Zephyrus', 'Katana', 'Stealth', 'Blade', 'Aorus', 'Helios Neo', 'Triton'],
        ['Raider GE', 'Vector GP', 'Crosshair', 'Elimina', 'Falcon', 'Phantom', 'Viper', 'Titan GT', 'Pulse GL', 'Cyborg'],
    ];

    private const BUSINESS_MODELS = [
        ['ThinkPad X', 'EliteBook', 'Latitude', 'Swift Go', 'ExpertBook', 'IdeaPad Pro', 'Galaxy Book', 'MateBook', 'LifeBook', 'TravelMate'],
        ['Spectre x', 'ZenBook', 'Portege', 'ThinkBook', 'ProBook', 'Inspiron', 'Vostro', 'Pavilion Plus', 'Modern', 'Prestige'],
    ];

    private const STUDENT_MODELS = [
        ['IdeaPad', 'Pavilion', 'Inspiron', 'Vivobook', 'Swift', 'MateBook D', 'Galaxy Book', 'Aspire', 'ThinkPad E', 'Surface Laptop'],
        ['Chromebook', 'Book', 'Leaf', 'Spin', 'Flex', 'Yoga', 'Duet', 'IdeaPad Flex', 'Pavilion x', 'Inspiron 2-in-1'],
    ];

    private const CPUS = [
        'Intel Core Ultra 9 285H', 'Intel Core Ultra 7 258V', 'Intel Core Ultra 5 226V',
        'Intel Core i9-14900HX', 'Intel Core i7-14700HX', 'Intel Core i5-14600HX',
        'Intel Core i7-13700H', 'Intel Core i5-13500H', 'Intel Core i3-1315U',
        'AMD Ryzen 9 7945HX', 'AMD Ryzen 7 8845HS', 'AMD Ryzen 5 8645HS',
        'AMD Ryzen 7 7735H', 'AMD Ryzen 5 7530U', 'AMD Ryzen 3 7330U',
        'Apple M4 Pro', 'Apple M4', 'Apple M3 Pro', 'Snapdragon X Elite', 'Snapdragon X Plus',
    ];

    private const RAM_OPTIONS = ['8GB DDR5', '16GB DDR5', '32GB DDR5', '64GB DDR5', '8GB LPDDR5', '16GB LPDDR5', '32GB LPDDR5'];
    private const STORAGE_OPTIONS = ['256GB SSD', '512GB SSD', '1TB SSD', '2TB SSD', '512GB NVMe', '1TB NVMe', '2TB NVMe'];
    private const DISPLAY_OPTIONS = [
        '13.3" FHD IPS', '13.5" 2K IPS', '14" FHD IPS', '14" 2.8K OLED', '14.5" FHD+',
        '15.6" FHD IPS', '15.6" 144Hz FHD', '16" QHD+ IPS', '16" 240Hz WQXGA', '16" 3.2K OLED',
        '17.3" FHD IPS', '17.3" 165Hz QHD',
    ];
    private const GPU_OPTIONS = [
        'Intel Arc Graphics', 'NVIDIA RTX 3050', 'NVIDIA RTX 4050', 'NVIDIA RTX 4060',
        'NVIDIA RTX 4070', 'NVIDIA RTX 4080', 'NVIDIA RTX 4090',
        'AMD Radeon 780M', 'AMD Radeon RX 7600S', 'AMD Radeon RX 7700S', 'AMD Radeon RX 7900M',
        'Apple M3 GPU (10-core)', 'Apple M4 GPU (10-core)',
    ];
    private const OS_OPTIONS = ['Windows 11 Home', 'Windows 11 Pro', 'Windows 10 Pro', 'macOS Sequoia', 'ChromeOS'];

    private const GAMING_GPU_BIAS = ['NVIDIA RTX 4060', 'NVIDIA RTX 4070', 'NVIDIA RTX 4080', 'NVIDIA RTX 4090', 'NVIDIA RTX 4050', 'NVIDIA RTX 3050', 'AMD Radeon RX 7600S', 'AMD Radeon RX 7700S', 'AMD Radeon RX 7900M'];
    private const MODELS_PER_CATEGORY = 5; // 5 gaming + 5 business + 5 student = 15

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $productRepo = $this->em->getRepository(Product::class);
        $slugger = new AsciiSlugger();

        $gamingCat = $categoryRepo->findOneBy(['slug' => 'gaming']);
        $businessCat = $categoryRepo->findOneBy(['slug' => 'business']);
        $studentCat = $categoryRepo->findOneBy(['slug' => 'student']);

        $created = 0;
        $skipped = 0;
        $failed = 0;

        $pools = [
            ['cat' => $gamingCat, 'models' => self::GAMING_MODELS, 'gpu_bias' => self::GAMING_GPU_BIAS],
            ['cat' => $businessCat, 'models' => self::BUSINESS_MODELS, 'gpu_bias' => ['Intel Arc Graphics', 'AMD Radeon 780M']],
            ['cat' => $studentCat, 'models' => self::STUDENT_MODELS, 'gpu_bias' => ['Intel Arc Graphics', 'AMD Radeon 780M']],
        ];

        foreach ($pools as $pool) {
            for ($i = 0; $i < self::MODELS_PER_CATEGORY; $i++) {
                $brand = self::BRANDS[array_rand(self::BRANDS)];
                $modelSet = $pool['models'][array_rand($pool['models'])];
                $modelName = $modelSet[array_rand($modelSet)];

                $screenSize = self::randomDisplaySize();
                $cpu = self::CPUS[array_rand(self::CPUS)];
                $ram = self::RAM_OPTIONS[array_rand(self::RAM_OPTIONS)];
                $storage = self::STORAGE_OPTIONS[array_rand(self::STORAGE_OPTIONS)];
                $display = self::DISPLAY_OPTIONS[array_rand(self::DISPLAY_OPTIONS)];
                $gpu = $pool['gpu_bias'][array_rand($pool['gpu_bias'])];
                $os = self::OS_OPTIONS[array_rand(self::OS_OPTIONS)];

                $productName = "{$brand} {$modelName} {$screenSize} {$cpu} {$ram} {$storage}";
                $slug = strtolower($slugger->slug($productName));
                $sku = strtoupper(substr($brand, 0, 3)) . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $modelName), 0, 6)) . '-' . str_pad((string) random_int(10, 999), 3, '0', STR_PAD_LEFT);
                $price = self::randomPrice($pool['gpu_bias'][0]);
                $stock = random_int(5, 25);
                $shortDesc = "{$cpu}, {$ram}, {$storage}, {$display}, {$gpu}, {$os}";
                $image = self::IMAGES[array_rand(self::IMAGES)];

                $existing = $productRepo->findOneBy(['slug' => $slug]);
                if ($existing) {
                    $output->writeln("<comment>SKIPPED:</comment> {$productName} (slug already exists)");
                    $skipped++;
                    continue;
                }

                try {
                    $product = new Product();
                    $product->setName($productName);
                    $product->setSlug($slug);
                    $product->setPrice($price);
                    if (random_int(0, 1)) {
                        $product->setSalePrice(round($price * (1 - random_int(5, 20) / 100), 2));
                    }
                    $product->setStock($stock);
                    $product->setSku($sku);
                    $product->setImageUrl($image);
                    $product->setShortDescription($shortDesc);
                    $product->setDescription("{$brand} {$modelName} — {$shortDesc}. A powerful laptop for everyday use.");
                    $product->setFeatured(false);
                    $product->setNewArrival(true);
                    $product->setLikesCount(0);
                    $product->setViewsCount(0);

                    if ($pool['cat']) {
                        $product->addCategory($pool['cat']);
                    }

                    $this->em->persist($product);
                    $created++;
                    $priceLabel = $product->getSalePrice() ? "\${$product->getSalePrice()} (was \${$price})" : "\${$price}";
                    $output->writeln("<info>CREATED:</info> {$productName} — {$priceLabel} [{$pool['cat']?->getName()}]");
                } catch (\Exception $e) {
                    $output->writeln("<error>FAILED:</error> {$productName} — {$e->getMessage()}");
                    $failed++;
                }
            }
        }

        $this->em->flush();
        $output->writeln("\n<info>Done! Created {$created} laptop products ({$skipped} skipped, {$failed} failed).</info>");

        return Command::SUCCESS;
    }

    private static function randomDisplaySize(): string
    {
        $sizes = ['13.3"', '13.5"', '14"', '14.5"', '15.6"', '16"', '17.3"'];
        return $sizes[array_rand($sizes)];
    }

    private static function randomPrice(string $gpuBias): float
    {
        return match (true) {
            str_contains($gpuBias, 'RTX 4090'), str_contains($gpuBias, 'RX 7900M') => round(random_int(2500, 4000) + random_int(0, 99) / 100, 2),
            str_contains($gpuBias, 'RTX 4080'), str_contains($gpuBias, 'RX 7700S') => round(random_int(1800, 2800) + random_int(0, 99) / 100, 2),
            str_contains($gpuBias, 'RTX 4070') => round(random_int(1200, 2000) + random_int(0, 99) / 100, 2),
            str_contains($gpuBias, 'RTX 4060'), str_contains($gpuBias, 'RX 7600S') => round(random_int(800, 1400) + random_int(0, 99) / 100, 2),
            str_contains($gpuBias, 'RTX 4050'), str_contains($gpuBias, 'RTX 3050') => round(random_int(550, 950) + random_int(0, 99) / 100, 2),
            str_contains($gpuBias, 'Arc'), str_contains($gpuBias, '780M') => round(random_int(350, 800) + random_int(0, 99) / 100, 2),
            str_contains($gpuBias, 'M3'), str_contains($gpuBias, 'M4') => round(random_int(900, 1800) + random_int(0, 99) / 100, 2),
            default => round(random_int(199, 2500) + random_int(0, 99) / 100, 2),
        };
    }
}
