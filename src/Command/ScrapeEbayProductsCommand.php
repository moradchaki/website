<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:scrape-ebay', description: 'Scrape eBay laptops and create products')]
class ScrapeEbayProductsCommand extends Command
{
    private const EBAY_URL = 'https://www.ebay.com/b/PC-Laptops-Netbooks/177/bn_317584';

    private const PRODUCTS = [
        ['name' => 'Samsung Galaxy Book4 15.6" Intel Core 7 512GB SSD', 'slug' => 'samsung-galaxy-book4', 'price' => 599.99, 'salePrice' => 499.99, 'stock' => 15, 'category' => 'business', 'image' => 'https://picsum.photos/seed/samsung-galaxy-book4/600/600', 'short' => 'Intel Core 7, 16GB RAM, 512GB SSD, 15.6" FHD Display, Windows 11 Pro', 'sku' => 'SAM-BK4-001'],
        ['name' => 'Dell Inspiron 15 3530 Touch i7-1355U 16GB 512GB', 'slug' => 'dell-inspiron-15-3530', 'price' => 679.00, 'salePrice' => 579.00, 'stock' => 20, 'category' => 'business', 'image' => 'https://picsum.photos/seed/dell-inspiron-15/600/600', 'short' => '13th Gen Intel i7, 16GB RAM, 512GB SSD, 15.6" Touchscreen, Windows 11 Pro', 'sku' => 'DEL-INSP-3530'],
        ['name' => 'HP Pavilion 15.6" AMD Ryzen 7 16GB RAM 512GB SSD', 'slug' => 'hp-pavilion-15-ryzen7', 'price' => 539.00, 'salePrice' => 439.00, 'stock' => 18, 'category' => 'student', 'image' => 'https://picsum.photos/seed/hp-pavilion-ryzen7/600/600', 'short' => 'AMD Ryzen 7 5825U, 16GB RAM, 512GB SSD, 15.6" FHD, Windows 11 Pro + Office', 'sku' => 'HP-PAV-5825'],
        ['name' => 'Lenovo IdeaPad 1 14" Intel 20GB RAM 1TB SSD', 'slug' => 'lenovo-ideapad-1-14', 'price' => 279.00, 'salePrice' => 249.00, 'stock' => 25, 'category' => 'student', 'image' => 'https://picsum.photos/seed/lenovo-ideapad-1/600/600', 'short' => 'Intel Processor, 20GB RAM, 1TB SSD, 14" Display, Windows 11 + Office 365', 'sku' => 'LEN-IDEAPAD-1'],
        ['name' => 'ASUS TUF Gaming A16 Ryzen 7 RX 7700S 16GB 512GB', 'slug' => 'asus-tuf-gaming-a16', 'price' => 849.99, 'salePrice' => 749.99, 'stock' => 10, 'category' => 'gaming', 'image' => 'https://picsum.photos/seed/asus-tuf-gaming-a16/600/600', 'short' => 'AMD Ryzen 7, 16GB RAM, 512GB SSD, AMD Radeon RX 7700S, 16" FHD', 'sku' => 'ASUS-TUF-A16'],
        ['name' => 'Acer Nitro V 15 ANV15-41 Ryzen 5 7535HS 16GB 512GB', 'slug' => 'acer-nitro-v-15', 'price' => 687.27, 'stock' => 12, 'category' => 'gaming', 'image' => 'https://picsum.photos/seed/acer-nitro-v-15/600/600', 'short' => 'AMD Ryzen 5 7535HS, 16GB RAM, 512GB SSD, 15.6" FHD, NVIDIA RTX 4050', 'sku' => 'ACER-NV-15'],
        ['name' => 'Lenovo Legion 7i 16" Gaming i7 14th Gen 16GB 1TB', 'slug' => 'lenovo-legion-7i-16', 'price' => 1299.99, 'salePrice' => 1199.99, 'stock' => 8, 'category' => 'gaming', 'image' => 'https://picsum.photos/seed/lenovo-legion-7i/600/600', 'short' => 'Intel 14th Gen Core i7, 16GB RAM, 1TB SSD, 16" WQXGA, RTX 4060', 'sku' => 'LEN-LEGION-7I'],
        ['name' => 'Apple MacBook Air M3 13.6" 16GB 512GB SSD 2024', 'slug' => 'macbook-air-m3-2024', 'price' => 879.00, 'stock' => 14, 'category' => 'business', 'image' => 'https://picsum.photos/seed/macbook-air-m3-2024/600/600', 'short' => 'Apple M3 chip, 16GB RAM, 512GB SSD, 13.6" Liquid Retina, Space Gray', 'sku' => 'APP-MBA-M3-24'],
        ['name' => 'ASUS Vivobook S15 OLED Snapdragon X Elite 32GB 1TB', 'slug' => 'asus-vivobook-s15-oled', 'price' => 633.00, 'salePrice' => 599.00, 'stock' => 11, 'category' => 'business', 'image' => 'https://picsum.photos/seed/asus-vivobook-s15/600/600', 'short' => 'Snapdragon X Elite X1E 78 100, 32GB RAM, 1TB SSD, 15.6" 3K OLED', 'sku' => 'ASUS-VIVO-S15'],
        ['name' => 'Acer Predator Helios 14" Ultra 7 16GB 1TB RTX 4060', 'slug' => 'acer-predator-helios-14', 'price' => 1000.00, 'salePrice' => 899.00, 'stock' => 6, 'category' => 'gaming', 'image' => 'https://picsum.photos/seed/acer-predator-helios/600/600', 'short' => 'Intel Core Ultra 7, 16GB RAM, 1TB SSD, 14.5" Display, RTX 4060', 'sku' => 'ACER-PH-14'],
        ['name' => 'Lenovo IdeaPad Slim 3 15ABR8 Ryzen 7 7730U 16GB 512GB', 'slug' => 'lenovo-ideapad-slim-3', 'price' => 393.87, 'stock' => 22, 'category' => 'student', 'image' => 'https://picsum.photos/seed/lenovo-ideapad-slim3/600/600', 'short' => 'AMD Ryzen 7 7730U, 16GB RAM, 512GB SSD, 15.6" FHD IPS, Windows 11', 'sku' => 'LEN-IPS3-15'],
        ['name' => 'HP Victus Gaming 15.6" Ryzen 5 7535HS 16GB 512GB', 'slug' => 'hp-victus-gaming-15', 'price' => 749.99, 'salePrice' => 649.99, 'stock' => 9, 'category' => 'gaming', 'image' => 'https://picsum.photos/seed/hp-victus-gaming/600/600', 'short' => 'AMD Ryzen 5 7535HS, 16GB RAM, 512GB SSD, 15.6" 144Hz FHD, Radeon RX 6550M', 'sku' => 'HP-VICTUS-15'],
        ['name' => 'Dell Latitude 14" Core i7 11th Gen 16GB 256GB SSD', 'slug' => 'dell-latitude-14-i7', 'price' => 279.99, 'stock' => 16, 'category' => 'business', 'image' => 'https://picsum.photos/seed/dell-latitude-i7/600/600', 'short' => 'Intel Core i7 11th Gen, 16GB RAM, 256GB SSD, 14" FHD, Windows 10 Pro', 'sku' => 'DEL-LAT-14'],
        ['name' => 'Microsoft Surface Laptop 7 13.8" Touch X Plus 16GB 256GB', 'slug' => 'surface-laptop-7', 'price' => 599.00, 'salePrice' => 549.00, 'stock' => 7, 'category' => 'business', 'image' => 'https://picsum.photos/seed/surface-laptop-7/600/600', 'short' => 'Snapdragon X Plus, 16GB RAM, 256GB SSD, 13.8" Touch, Platinum, Windows 11', 'sku' => 'MS-SL7-001'],
        ['name' => 'Lenovo ThinkPad E495 14" Ryzen 5 16GB 256GB SSD', 'slug' => 'lenovo-thinkpad-e495', 'price' => 199.99, 'stock' => 19, 'category' => 'business', 'image' => 'https://picsum.photos/seed/lenovo-thinkpad-e495/600/600', 'short' => 'AMD Ryzen 5, 16GB RAM, 256GB SSD, 14" HD, Windows 10 Pro, Business Laptop', 'sku' => 'LEN-TP-E495'],
    ];

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $productRepo = $this->em->getRepository(Product::class);

        $created = 0;
        foreach (self::PRODUCTS as $data) {
            $existing = $productRepo->findOneBy(['slug' => $data['slug']]);
            if ($existing) {
                $output->writeln("<comment>SKIPPED:</comment> {$data['name']} (already exists)");
                continue;
            }

            $category = $categoryRepo->findOneBy(['slug' => $data['category']]);

            $product = new Product();
            $product->setName($data['name']);
            $product->setSlug($data['slug']);
            $product->setPrice($data['price']);
            if (isset($data['salePrice'])) {
                $product->setSalePrice($data['salePrice']);
            }
            $product->setStock($data['stock']);
            $product->setSku($data['sku']);
            $product->setImageUrl($data['image']);
            $product->setShortDescription($data['short']);
            $product->setDescription($data['short'] . '. Premium quality laptop from eBay selection.');
            $product->setFeatured(false);
            $product->setNewArrival(true);
            $product->setLikesCount(0);
            $product->setViewsCount(0);

            if ($category) {
                $product->addCategory($category);
            }

            $this->em->persist($product);
            $created++;
            $output->writeln("<info>CREATED:</info> {$data['name']} (\${$data['price']})");
        }

        $this->em->flush();
        $output->writeln("<info>Done! Created {$created} new products.</info>");

        return Command::SUCCESS;
    }
}
