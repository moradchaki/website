<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-products', description: 'Add 50 new laptop products to the catalog')]
class AddProductsCommand extends Command
{
    private const PRODUCTS = [
        'gaming' => [
            ['name' => 'ROG Strix Scar 18" i9-14900HX RTX 4090 32GB 2TB', 'slug' => 'rog-strix-scar-18', 'price' => 3499.00, 'salePrice' => 2999.00, 'stock' => 8, 'sku' => 'ASUS-SCAR-18', 'short' => 'i9-14900HX, RTX 4090, 32GB DDR5, 2TB NVMe, 18" 240Hz QHD'],
            ['name' => 'MSI Titan GT77 HX i9-14900HX RTX 4080 64GB 2TB', 'slug' => 'msi-titan-gt77', 'price' => 2899.99, 'stock' => 5, 'sku' => 'MSI-TITAN-77', 'short' => 'i9-14900HX, RTX 4080, 64GB DDR5, 2TB NVMe, 17.3" 4K 144Hz'],
            ['name' => 'Razer Blade 16" i9-14900H RTX 4070 16GB 1TB', 'slug' => 'razer-blade-16-2024', 'price' => 2699.99, 'salePrice' => 2399.99, 'stock' => 6, 'sku' => 'RAZ-BLADE-16', 'short' => 'i9-14900H, RTX 4070, 16GB DDR5, 1TB NVMe, 16" QHD+ 240Hz OLED'],
            ['name' => 'Gigabyte Aorus 17X i7-14700HX RTX 4060 16GB 1TB', 'slug' => 'gigabyte-aorus-17x', 'price' => 1799.00, 'stock' => 10, 'sku' => 'GIG-AORUS-17X', 'short' => 'i7-14700HX, RTX 4060, 16GB DDR5, 1TB NVMe, 17.3" FHD 360Hz'],
            ['name' => 'ASUS TUF Dash F15 i7-13620H RTX 4050 16GB 512GB', 'slug' => 'asus-tuf-dash-f15', 'price' => 999.99, 'salePrice' => 899.99, 'stock' => 15, 'sku' => 'ASUS-TUF-F15', 'short' => 'i7-13620H, RTX 4050, 16GB DDR5, 512GB NVMe, 15.6" FHD 144Hz'],
            ['name' => 'Acer Helios 16" i9-14900HX RTX 4070 32GB 1TB', 'slug' => 'acer-helios-16', 'price' => 2199.00, 'stock' => 7, 'sku' => 'ACR-HEL-16', 'short' => 'i9-14900HX, RTX 4070, 32GB DDR5, 1TB NVMe, 16" WQXGA 240Hz'],
            ['name' => 'Lenovo Legion Pro 5 Ryzen 9 7945HX RTX 4070 32GB 1TB', 'slug' => 'lenovo-legion-pro-5', 'price' => 1999.99, 'salePrice' => 1799.99, 'stock' => 9, 'sku' => 'LEN-LEG-P5', 'short' => 'Ryzen 9 7945HX, RTX 4070, 32GB DDR5, 1TB NVMe, 16" WQXGA 165Hz'],
            ['name' => 'MSI Stealth 14 i7-13700H RTX 4060 16GB 512GB', 'slug' => 'msi-stealth-14', 'price' => 1499.00, 'stock' => 11, 'sku' => 'MSI-STL-14', 'short' => 'i7-13700H, RTX 4060, 16GB DDR5, 512GB NVMe, 14" QHD+ 165Hz'],
            ['name' => 'HP OMEN Transcend 16 i9-13900H RTX 4060 16GB 1TB', 'slug' => 'hp-omen-transcend-16', 'price' => 1699.99, 'salePrice' => 1549.99, 'stock' => 8, 'sku' => 'HP-OMEN-16', 'short' => 'i9-13900H, RTX 4060, 16GB DDR5, 1TB NVMe, 16" 240Hz FHD IPS'],
            ['name' => 'Dell Alienware m18 Ryzen 9 7945HX RTX 4080 32GB 1TB', 'slug' => 'alienware-m18', 'price' => 2599.00, 'stock' => 4, 'sku' => 'DEL-ALW-M18', 'short' => 'Ryzen 9 7945HX, RTX 4080, 32GB DDR5, 1TB NVMe, 18" FHD 480Hz'],
        ],
        'business' => [
            ['name' => 'Dell Latitude 7450 Core Ultra 7 16GB 512GB Touch', 'slug' => 'dell-latitude-7450', 'price' => 1549.00, 'stock' => 12, 'sku' => 'DEL-LAT-7450', 'short' => 'Core Ultra 7 165H, 16GB LPDDR5X, 512GB NVMe, 14" FHD+ Touch, Win 11 Pro'],
            ['name' => 'HP EliteBook 840 G11 Core Ultra 7 32GB 512GB', 'slug' => 'hp-elitebook-840-g11', 'price' => 1799.00, 'salePrice' => 1649.00, 'stock' => 8, 'sku' => 'HP-ELT-840', 'short' => 'Core Ultra 7 165H, 32GB LPDDR5X, 512GB NVMe, 14" 2.8K OLED, Win 11 Pro'],
            ['name' => 'Lenovo ThinkPad X13 Gen 5 AMD Ryzen 7 16GB 256GB', 'slug' => 'thinkpad-x13-gen5', 'price' => 1299.00, 'stock' => 14, 'sku' => 'LEN-TPX13-G5', 'short' => 'Ryzen 7 PRO 8840U, 16GB LPDDR5X, 256GB NVMe, 13.3" WUXGA IPS, Win 11 Pro'],
            ['name' => 'Microsoft Surface Laptop 6 i7-1365U 16GB 512GB', 'slug' => 'surface-laptop-6', 'price' => 1399.99, 'salePrice' => 1249.99, 'stock' => 10, 'sku' => 'MS-SL6-001', 'short' => 'i7-1365U, 16GB LPDDR5, 512GB NVMe, 15" PixelSense Touch, Win 11 Pro'],
            ['name' => 'ASUS ExpertBook B9 OLED Core Ultra 7 16GB 512GB', 'slug' => 'asus-expertbook-b9', 'price' => 1699.00, 'stock' => 7, 'sku' => 'ASUS-EXP-B9', 'short' => 'Core Ultra 7 155H, 16GB LPDDR5X, 512GB NVMe, 14" 2.8K OLED, Win 11 Pro'],
            ['name' => 'Fujitsu LifeBook U7413 Core i7-1365U 16GB 256GB', 'slug' => 'fujitsu-lifebook-u7413', 'price' => 1149.00, 'stock' => 9, 'sku' => 'FUJ-LB-U7413', 'short' => 'i7-1365U, 16GB DDR5, 256GB NVMe, 14" FHD IPS, Win 10 Pro'],
            ['name' => 'Samsung Galaxy Book4 Pro 360 i7-1360P 16GB 512GB', 'slug' => 'samsung-galaxy-book4-pro-360', 'price' => 1449.99, 'salePrice' => 1299.99, 'stock' => 11, 'sku' => 'SAM-GB4-360', 'short' => 'i7-1360P, 16GB LPDDR5, 512GB NVMe, 16" AMOLED Touch, Win 11, S Pen'],
            ['name' => 'Toshiba Dynabook Tecra A40-M Core i7-1355U 16GB 512GB', 'slug' => 'toshiba-tecra-a40', 'price' => 1049.00, 'stock' => 13, 'sku' => 'TOS-TEC-A40', 'short' => 'i7-1355U, 16GB DDR4, 512GB NVMe, 14" FHD IPS, Win 11 Pro'],
            ['name' => 'Panasonic Toughbook 40 Mk2 Core i7-13800H 32GB 512GB', 'slug' => 'panasonic-toughbook-40', 'price' => 4299.00, 'stock' => 3, 'sku' => 'PAN-TB40-MK2', 'short' => 'i7-13800H, 32GB DDR5, 512GB NVMe, 14" FHD Touch, Win 11 Pro, MIL-STD-810H'],
            ['name' => 'Huawei MateBook X Pro Core Ultra 7 16GB 1TB', 'slug' => 'huawei-matebook-x-pro', 'price' => 1599.00, 'stock' => 8, 'sku' => 'HUA-MB-XPRO', 'short' => 'Core Ultra 7 155H, 16GB LPDDR5X, 1TB NVMe, 14.2" 3.1K OLED Touch, Win 11'],
        ],
        'student' => [
            ['name' => 'Acer Aspire 5 Ryzen 5 7520U 8GB 256GB', 'slug' => 'acer-aspire-5-ryzen', 'price' => 449.99, 'salePrice' => 399.99, 'stock' => 20, 'sku' => 'ACR-ASP-5-R5', 'short' => 'Ryzen 5 7520U, 8GB DDR5, 256GB NVMe, 15.6" FHD IPS, Win 11 Home'],
            ['name' => 'HP Pavilion 14 i5-1335U 8GB 256GB', 'slug' => 'hp-pavilion-14-i5', 'price' => 529.00, 'stock' => 18, 'sku' => 'HP-PAV-14-I5', 'short' => 'i5-1335U, 8GB DDR4, 256GB NVMe, 14" FHD IPS, Win 11 Home + Office 365'],
            ['name' => 'Lenovo IdeaPad Flex 5 Ryzen 5 7530U 16GB 512GB 2-in-1', 'slug' => 'lenovo-ideapad-flex-5', 'price' => 649.99, 'salePrice' => 599.99, 'stock' => 14, 'sku' => 'LEN-IP-FLEX5', 'short' => 'Ryzen 5 7530U, 16GB DDR4, 512GB NVMe, 16" WUXGA Touch, Win 11 Home'],
            ['name' => 'Dell Inspiron 16 5635 Ryzen 5 7530U 8GB 512GB', 'slug' => 'dell-inspiron-16-5635', 'price' => 579.99, 'stock' => 16, 'sku' => 'DEL-INS-5635', 'short' => 'Ryzen 5 7530U, 8GB DDR4, 512GB NVMe, 16" FHD+ IPS, Win 11 Home'],
            ['name' => 'ASUS Vivobook 15 i3-1315U 8GB 256GB', 'slug' => 'asus-vivobook-15-i3', 'price' => 379.00, 'salePrice' => 349.00, 'stock' => 22, 'sku' => 'ASUS-VIVO-15-I3', 'short' => 'i3-1315U, 8GB DDR4, 256GB NVMe, 15.6" FHD IPS, Win 11 Home'],
            ['name' => 'Acer Chromebook 514 Ryzen 3 7320C 8GB 128GB', 'slug' => 'acer-chromebook-514', 'price' => 349.99, 'stock' => 25, 'sku' => 'ACR-CB-514', 'short' => 'Ryzen 3 7320C, 8GB LPDDR5, 128GB eMMC, 14" FHD IPS, ChromeOS'],
            ['name' => 'HP Envy x360 15 Ryzen 7 8840HS 16GB 512GB 2-in-1', 'slug' => 'hp-envy-x360-15', 'price' => 899.00, 'salePrice' => 799.00, 'stock' => 10, 'sku' => 'HP-ENVY-X360', 'short' => 'Ryzen 7 8840HS, 16GB LPDDR5X, 512GB NVMe, 15.6" FHD Touch, Win 11 Home'],
            ['name' => 'Microsoft Surface Go 4 i3-N305 8GB 128GB', 'slug' => 'surface-go-4', 'price' => 579.99, 'stock' => 12, 'sku' => 'MS-SG4-001', 'short' => 'i3-N305, 8GB LPDDR5, 128GB eMMC, 10.5" PixelSense, Win 11 Home + Keyboard'],
            ['name' => 'Samsung Galaxy Book3 15.6" i5-1335U 8GB 256GB', 'slug' => 'samsung-galaxy-book3', 'price' => 499.00, 'salePrice' => 449.00, 'stock' => 17, 'sku' => 'SAM-GB3-001', 'short' => 'i5-1335U, 8GB DDR4, 256GB NVMe, 15.6" FHD IPS, Win 11 Home'],
            ['name' => 'Google Pixelbook Go Core i5-1245U 8GB 128GB', 'slug' => 'google-pixelbook-go', 'price' => 649.00, 'stock' => 9, 'sku' => 'GOOG-PBG-001', 'short' => 'i5-1245U, 8GB LPDDR5, 128GB NVMe, 13.3" FHD Touch, ChromeOS'],
        ],
        'workstations' => [
            ['name' => 'HP ZBook Power G11 i7-14700HX RTX 3000 Ada 32GB 1TB', 'slug' => 'hp-zbook-power-g11', 'price' => 2199.00, 'stock' => 6, 'sku' => 'HP-ZBP-G11', 'short' => 'i7-14700HX, RTX 3000 Ada, 32GB DDR5, 1TB NVMe, 16" QHD IPS, Win 11 Pro'],
            ['name' => 'Lenovo ThinkPad P1 Gen 7 i9-14900HX RTX 3500 Ada 64GB 2TB', 'slug' => 'thinkpad-p1-gen7', 'price' => 3299.00, 'salePrice' => 2999.00, 'stock' => 4, 'sku' => 'LEN-TPP1-G7', 'short' => 'i9-14900HX, RTX 3500 Ada, 64GB DDR5 ECC, 2TB NVMe, 16" 4K OLED, Win 11 Pro'],
            ['name' => 'Dell Precision 5680 i9-13900H RTX 5000 Ada 64GB 2TB', 'slug' => 'dell-precision-5680', 'price' => 4599.00, 'stock' => 3, 'sku' => 'DEL-PRC-5680', 'short' => 'i9-13900H, RTX 5000 Ada, 64GB DDR5, 2TB NVMe, 16" UHD+ IPS, Win 11 Pro'],
            ['name' => 'MSI Creator Z17 HX i9-14900HX RTX 4070 32GB 1TB', 'slug' => 'msi-creator-z17', 'price' => 2499.99, 'salePrice' => 2299.99, 'stock' => 5, 'sku' => 'MSI-CRZ-17', 'short' => 'i9-14900HX, RTX 4070, 32GB DDR5, 1TB NVMe, 17" QHD+ 165Hz, Win 11 Pro'],
            ['name' => 'ASUS ProArt P16 Ryzen 9 7945HX RTX 4070 32GB 1TB', 'slug' => 'asus-proart-p16', 'price' => 2299.00, 'stock' => 7, 'sku' => 'ASUS-PA-P16', 'short' => 'Ryzen 9 7945HX, RTX 4070, 32GB DDR5, 1TB NVMe, 16" 4K OLED, Win 11 Pro'],
            ['name' => 'Acer ConceptD 7 Ezel i9-13900H RTX 3080 32GB 1TB', 'slug' => 'acer-conceptd-7-ezel', 'price' => 2799.00, 'stock' => 4, 'sku' => 'ACR-CDN-7EZ', 'short' => 'i9-13900H, RTX 3080, 32GB DDR5, 1TB NVMe, 15.6" 4K Touch, Win 10 Pro'],
            ['name' => 'Fujitsu Celsius H970 i7-13700H RTX A2000 32GB 512GB', 'slug' => 'fujitsu-celsius-h970', 'price' => 2599.00, 'stock' => 5, 'sku' => 'FUJ-CEL-H970', 'short' => 'i7-13700H, RTX A2000, 32GB DDR5, 512GB NVMe, 17.3" FHD IPS, Win 11 Pro'],
            ['name' => 'Panasonic CF-33 Mk2 i5-1245U 16GB 256GB Rugged', 'slug' => 'panasonic-cf33-mk2', 'price' => 3899.00, 'stock' => 3, 'sku' => 'PAN-CF33-MK2', 'short' => 'i5-1245U, 16GB DDR5, 256GB NVMe, 12" FHD Touch, Win 10 Pro, MIL-STD-810H'],
            ['name' => 'Samsung Galaxy Book4 Ultra i9-14900HX RTX 4070 32GB 1TB', 'slug' => 'samsung-galaxy-book4-ultra', 'price' => 2399.99, 'salePrice' => 2199.99, 'stock' => 6, 'sku' => 'SAM-GB4-ULTRA', 'short' => 'i9-14900HX, RTX 4070, 32GB LPDDR5X, 1TB NVMe, 16" 3K AMOLED, Win 11 Pro'],
            ['name' => 'ASUS ROG Flow Z13 Ryzen 9 7940HS RTX 4060 16GB 1TB', 'slug' => 'asus-rog-flow-z13', 'price' => 1899.00, 'stock' => 8, 'sku' => 'ASUS-ROG-Z13', 'short' => 'Ryzen 9 7940HS, RTX 4060, 16GB DDR5, 1TB NVMe, 13.4" FHD+ 165Hz Touch, Win 11'],
        ],
        'accessories' => [
            ['name' => 'CalDigit TS4 Thunderbolt 4 Dock', 'slug' => 'caldigit-ts4-dock', 'price' => 379.99, 'stock' => 15, 'sku' => 'CAL-TS4-001', 'short' => 'Thunderbolt 4, 98W PD, 18 ports, dual 8K@60Hz, 2.5GbE'],
            ['name' => 'Logitech MX Keys S Advanced Wireless Keyboard', 'slug' => 'logitech-mx-keys-s', 'price' => 119.99, 'salePrice' => 99.99, 'stock' => 25, 'sku' => 'LOG-MXK-S', 'short' => 'Full-size, backlit, USB-C/Bluetooth, multi-device, 10-day battery'],
            ['name' => 'Razer DeathAdder V3 Pro Wireless Mouse', 'slug' => 'razer-deathadder-v3-pro', 'price' => 149.99, 'stock' => 18, 'sku' => 'RAZ-DV3-PRO', 'short' => '63g ultra-light, 30K DPI Focus Pro, 90hr battery, USB-C/Bluetooth'],
            ['name' => 'Samsung 49" Odyssey G9 OLED Curved Monitor', 'slug' => 'samsung-odyssey-g9-oled', 'price' => 1599.99, 'salePrice' => 1399.99, 'stock' => 5, 'sku' => 'SAM-ODY-G9', 'short' => '49" DQHD 5120x1440, 240Hz, 0.03ms GtG, OLED, HDR400 True Black'],
            ['name' => 'Logitech StreamCam Plus 4K Webcam', 'slug' => 'logitech-streamcam-plus', 'price' => 169.99, 'stock' => 12, 'sku' => 'LOG-SCP-4K', 'short' => '4K@30fps, 1080p@60fps, USB-C, auto-focus, dual omnidirectional mics'],
            ['name' => 'Jabra Evolve2 85 Wireless ANC Headset', 'slug' => 'jabra-evolve2-85', 'price' => 349.00, 'salePrice' => 299.00, 'stock' => 9, 'sku' => 'JAB-EV2-85', 'short' => 'Hybrid ANC, 37hr battery, Microsoft Teams certified, USB-C/Bluetooth'],
            ['name' => 'Anker PowerCore 26800mAh USB-C Power Bank', 'slug' => 'anker-powercore-26800', 'price' => 65.99, 'stock' => 30, 'sku' => 'ANK-PC-26800', 'short' => '26800mAh, dual USB-C 60W PD, USB-A, fast charge, airline-safe'],
            ['name' => 'Herman Miller Logitech Embody Gaming Chair', 'slug' => 'herman-miller-embody-gaming', 'price' => 1795.00, 'stock' => 4, 'sku' => 'HM-EMBODY-LOGI', 'short' => 'Ergonomic gaming chair, Pixelated Support, cooling foam, 12yr warranty'],
            ['name' => 'Elgato Stream Deck MK.2 15-Key Studio Controller', 'slug' => 'elgato-stream-deck-mk2', 'price' => 149.99, 'stock' => 20, 'sku' => 'ELG-SD-MK2', 'short' => '15 customizable LCD keys, USB-C, drag-and-drop setup, OBS integration'],
            ['name' => 'CableMod Pro ModMesh Cable Kit - White', 'slug' => 'cablemod-promesh-white', 'price' => 89.99, 'stock' => 14, 'sku' => 'CBM-PM-WHITE', 'short' => 'Premium sleeved cable kit, ATX 3.0 compatible, 24-pin MB + 4 PCIe + 2 EPS'],
        ],
    ];

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

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $productRepo = $this->em->getRepository(Product::class);
        $imgIdx = 0;

        $created = 0;
        $skipped = 0;

        foreach (self::PRODUCTS as $catSlug => $products) {
            $category = $categoryRepo->findOneBy(['slug' => $catSlug]);
            if (!$category) {
                $output->writeln("<error>Category '{$catSlug}' not found. Skipping.</error>");
                continue;
            }

            foreach ($products as $data) {
                $existing = $productRepo->findOneBy(['slug' => $data['slug']]);
                if ($existing) {
                    $output->writeln("<comment>SKIPPED:</comment> {$data['name']} (already exists)");
                    $skipped++;
                    continue;
                }

                $product = new Product();
                $product->setName($data['name']);
                $product->setSlug($data['slug']);
                $product->setPrice($data['price']);
                if (isset($data['salePrice'])) {
                    $product->setSalePrice($data['salePrice']);
                }
                $product->setStock($data['stock']);
                $product->setSku($data['sku']);
                $product->setShortDescription($data['short']);
                $product->setDescription("{$data['name']} — {$data['short']}. Engineered for peak performance and reliability.");
                $product->setImageUrl(self::IMAGES[$imgIdx % count(self::IMAGES)]);
                $product->setFeatured(false);
                $product->setNewArrival(true);
                $product->setLikesCount(0);
                $product->setViewsCount(0);
                $product->addCategory($category);

                $this->em->persist($product);
                $created++;
                $imgIdx++;
                $priceLabel = isset($data['salePrice']) ? "\${$data['salePrice']} (was \${$data['price']})" : "\${$data['price']}";
                $output->writeln("<info>CREATED [{$catSlug}]:</info> {$data['name']} — {$priceLabel}");
            }
        }

        $this->em->flush();
        $output->writeln("\n<info>Done! Created {$created} products ({$skipped} skipped).</info>");

        return Command::SUCCESS;
    }
}
