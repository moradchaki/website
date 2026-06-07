<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Category;
use App\Entity\FlashDeal;
use App\Entity\NewsletterSubscription;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderStatus;
use App\Entity\Product;
use App\Entity\ProductComparison;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const CATEGORIES = [
        ['name' => 'Gaming', 'slug' => 'gaming', 'desc' => 'Uncompromising frame rates and liquid-smooth visuals for the serious competitor.', 'order' => 1],
        ['name' => 'Business', 'slug' => 'business', 'desc' => 'Engineered for the modern professional. Military-grade durability meets enterprise security.', 'order' => 2],
        ['name' => 'Student', 'slug' => 'student', 'desc' => 'Built for the grind. Performance you need at prices that respect your budget.', 'order' => 3],
        ['name' => 'Workstations', 'slug' => 'workstations', 'desc' => 'Professional workstations engineered for 3D rendering, AI training, and video production.', 'order' => 4],
        ['name' => 'Accessories', 'slug' => 'accessories', 'desc' => 'Every component of your workflow, refined.', 'order' => 5],
    ];

    private const PRODUCTS = [
        'gaming' => [
            [
                'name' => 'Lumina Blade 14"',
                'slug' => 'lumina-blade-14',
                'desc' => 'Ultra-slim gaming laptop with RTX 4070 and 165Hz QHD display. Precision-milled aluminum chassis with customizable RGB keyboard.',
                'short' => 'RTX 4070 · 165Hz QHD · 32GB DDR5',
                'price' => 2499, 'sale' => 1899,
                'sku' => 'LMB-14-001', 'featured' => true, 'new' => true,
                'specs' => ['cpu' => 'Intel Core i9-14900H', 'ram' => '32GB DDR5', 'storage' => '1TB NVMe SSD', 'gpu' => 'NVIDIA RTX 4070', 'display' => '14" QHD 165Hz OLED', 'weight' => '1.6kg'],
            ],
            [
                'name' => 'Titan X Pro',
                'slug' => 'titan-x-pro',
                'desc' => 'Flagship gaming workstation with desktop-grade RTX 5090. Mechanical per-key RGB and vapor chamber cooling.',
                'short' => 'RTX 5090 · 240Hz 4K · 64GB DDR5',
                'price' => 3700, 'sale' => 3150,
                'sku' => 'TXP-17-001', 'featured' => true, 'new' => false,
                'specs' => ['cpu' => 'Intel Core i9-14900HX', 'ram' => '64GB DDR5', 'storage' => '2TB NVMe SSD', 'gpu' => 'NVIDIA RTX 5090', 'display' => '17" 4K 240Hz Mini LED', 'weight' => '2.8kg'],
            ],
            [
                'name' => 'Vector 15s',
                'slug' => 'vector-15s',
                'desc' => 'Mid-range gaming beast with RTX 4060 and 144Hz FHD display. Perfect for competitive esports.',
                'short' => 'RTX 4060 · 144Hz FHD · 16GB DDR5',
                'price' => 1999, 'sale' => 1299,
                'sku' => 'VEC-15-001', 'featured' => false, 'new' => false,
                'specs' => ['cpu' => 'AMD Ryzen 7 8845HS', 'ram' => '16GB DDR5', 'storage' => '512GB NVMe SSD', 'gpu' => 'NVIDIA RTX 4060', 'display' => '15.6" FHD 144Hz IPS', 'weight' => '2.1kg'],
            ],
            [
                'name' => 'Nexus G1',
                'slug' => 'nexus-g1',
                'desc' => 'Entry-level gaming laptop with RTX 3050. Great for casual gaming and content consumption.',
                'short' => 'RTX 3050 · 120Hz FHD · 8GB DDR5',
                'price' => 2699, 'sale' => 2429,
                'sku' => 'NXG-16-001', 'featured' => false, 'new' => true,
                'specs' => ['cpu' => 'Intel Core i7-14650HX', 'ram' => '8GB DDR5', 'storage' => '256GB NVMe SSD', 'gpu' => 'NVIDIA RTX 3050', 'display' => '16" FHD 120Hz IPS', 'weight' => '2.3kg'],
            ],
        ],
        'business' => [
            [
                'name' => 'EliteBook Ultra G3',
                'slug' => 'elitebook-ultra-g3',
                'desc' => 'Premium business ultrabook with Intel Core Ultra 9. MIL-STD-810H certified with enterprise-grade security.',
                'short' => 'Core Ultra 9 · 32GB · 1TB SSD',
                'price' => 2299, 'sale' => null,
                'sku' => 'EBU-G3-001', 'featured' => true, 'new' => true,
                'specs' => ['cpu' => 'Intel Core Ultra 9 285H', 'ram' => '32GB LPDDR5X', 'storage' => '1TB NVMe SSD', 'gpu' => 'Intel Arc Graphics', 'display' => '14" 2.8K OLED 120Hz', 'weight' => '1.2kg'],
            ],
            [
                'name' => 'ThinkPad X1 Carbon Gen 12',
                'slug' => 'thinkpad-x1-carbon-gen12',
                'desc' => 'The legendary ThinkPad, reimagined. Carbon fiber chassis with 4G LTE and 18-hour battery life.',
                'short' => 'Core Ultra 7 · 16GB · 512GB SSD',
                'price' => 1899, 'sale' => null,
                'sku' => 'TPC-G12-001', 'featured' => true, 'new' => false,
                'specs' => ['cpu' => 'Intel Core Ultra 7 265H', 'ram' => '16GB LPDDR5X', 'storage' => '512GB NVMe SSD', 'gpu' => 'Intel Arc Graphics', 'display' => '14" 1920x1200 IPS', 'weight' => '1.08kg'],
            ],
            [
                'name' => 'Latitude 9550',
                'slug' => 'latitude-9550',
                'desc' => 'Dell flagship business laptop with InfinityEdge display and Express Response quick-charge.',
                'short' => 'Core Ultra 7 · 16GB · 256GB SSD',
                'price' => 1699, 'sale' => null,
                'sku' => 'LAT-9550-001', 'featured' => false, 'new' => false,
                'specs' => ['cpu' => 'Intel Core Ultra 7 265H', 'ram' => '16GB LPDDR5X', 'storage' => '256GB NVMe SSD', 'gpu' => 'Intel Arc Graphics', 'display' => '15" FHD+ InfinityEdge', 'weight' => '1.42kg'],
            ],
        ],
        'student' => [
            [
                'name' => 'Lumina Book 14',
                'slug' => 'lumina-book-14',
                'desc' => 'Affordable and lightweight laptop perfect for note-taking, research, and presentations.',
                'short' => 'Core i5 · 16GB · 256GB SSD',
                'price' => 899, 'sale' => null,
                'sku' => 'LMB-14S-001', 'featured' => true, 'new' => true,
                'specs' => ['cpu' => 'Intel Core i5-13500H', 'ram' => '16GB DDR4', 'storage' => '256GB NVMe SSD', 'gpu' => 'Intel Iris Xe', 'display' => '14" FHD IPS', 'weight' => '1.4kg'],
            ],
            [
                'name' => 'Aspire Vero 16',
                'slug' => 'aspire-vero-16',
                'desc' => 'Eco-friendly laptop made from recycled materials. Powerful AMD Ryzen performance for under $800.',
                'short' => 'Ryzen 7 · 16GB · 512GB SSD',
                'price' => 749, 'sale' => null,
                'sku' => 'ASV-16-001', 'featured' => false, 'new' => false,
                'specs' => ['cpu' => 'AMD Ryzen 7 8840U', 'ram' => '16GB DDR5', 'storage' => '512GB NVMe SSD', 'gpu' => 'AMD Radeon Graphics', 'display' => '16" WUXGA IPS', 'weight' => '1.8kg'],
            ],
            [
                'name' => 'IdeaPad Slim 5',
                'slug' => 'ideapad-slim-5',
                'desc' => 'Budget-friendly everyday laptop for classes, streaming, and light productivity.',
                'short' => 'Core i5 · 8GB · 256GB SSD',
                'price' => 599, 'sale' => null,
                'sku' => 'IPS-5-001', 'featured' => false, 'new' => false,
                'specs' => ['cpu' => 'Intel Core i5-13420H', 'ram' => '8GB DDR4', 'storage' => '256GB NVMe SSD', 'gpu' => 'Intel UHD Graphics', 'display' => '15.6" FHD IPS', 'weight' => '1.6kg'],
            ],
        ],
        'workstations' => [
            [
                'name' => 'Precision 7780',
                'slug' => 'precision-7780',
                'desc' => 'Ultimate mobile workstation with RTX 5000 Ada and 64GB ECC memory. ISV certified for all major CAD suites.',
                'short' => 'Core i9 · 64GB · 2TB · RTX 5000 Ada',
                'price' => 4899, 'sale' => null,
                'sku' => 'PRC-7780-001', 'featured' => true, 'new' => true,
                'specs' => ['cpu' => 'Intel Core i9-14900HX', 'ram' => '64GB DDR5 ECC', 'storage' => '2TB NVMe SSD', 'gpu' => 'NVIDIA RTX 5000 Ada', 'display' => '17" UHD+ IPS 120Hz', 'weight' => '3.2kg'],
            ],
            [
                'name' => 'ThinkPad P16 Gen 2',
                'slug' => 'thinkpad-p16-gen2',
                'desc' => 'Lenovo mobile workstation with RTX 3500 Ada. Perfect for engineers and architects on the go.',
                'short' => 'Core i7 · 32GB · 1TB · RTX 3500 Ada',
                'price' => 3499, 'sale' => null,
                'sku' => 'TPP-G2-001', 'featured' => true, 'new' => false,
                'specs' => ['cpu' => 'Intel Core i7-14700HX', 'ram' => '32GB DDR5 ECC', 'storage' => '1TB NVMe SSD', 'gpu' => 'NVIDIA RTX 3500 Ada', 'display' => '16" QHD+ IPS 165Hz', 'weight' => '2.9kg'],
            ],
            [
                'name' => 'ZBook Fury G11',
                'slug' => 'zbook-fury-g11',
                'desc' => 'HP flagship workstation with DreamColor display. Color-accurate for filmmakers and photographers.',
                'short' => 'Core i9 · 32GB · 1TB · RTX 4000 Ada',
                'price' => 3899, 'sale' => null,
                'sku' => 'ZBF-G11-001', 'featured' => false, 'new' => false,
                'specs' => ['cpu' => 'Intel Core i9-14900HX', 'ram' => '32GB DDR5', 'storage' => '1TB NVMe SSD', 'gpu' => 'NVIDIA RTX 4000 Ada', 'display' => '16" 4K DreamColor', 'weight' => '2.8kg'],
            ],
        ],
        'accessories' => [
            [
                'name' => 'Thunderbolt 4 Dock Pro',
                'slug' => 'thunderbolt-4-dock-pro',
                'desc' => 'Universal Thunderbolt 4 dock with triple 4K output, 96W power delivery, and 10Gbps USB ports.',
                'short' => 'Triple 4K · 96W charging · 10Gbps USB',
                'price' => 329, 'sale' => null,
                'sku' => 'TB4-DOCK-001', 'featured' => true, 'new' => true,
                'specs' => ['ports' => '3x USB-C, 2x USB-A, HDMI, DP, Ethernet', 'charging' => '96W PD', 'video' => 'Triple 4K@60Hz', 'interface' => 'Thunderbolt 4 / USB4'],
            ],
            [
                'name' => 'Elite Mechanical KB',
                'slug' => 'elite-mechanical-kb',
                'desc' => 'Hot-swappable mechanical keyboard with aluminum frame and per-key RGB. Cherry MX switches.',
                'short' => 'Hot-swappable · Aluminum · RGB',
                'price' => 199, 'sale' => null,
                'sku' => 'EMK-001', 'featured' => false, 'new' => false,
                'specs' => ['switches' => 'Cherry MX Red (hot-swappable)', 'layout' => 'TKL (Tenkeyless)', 'connection' => 'USB-C / Bluetooth 5.2', 'battery' => '4000mAh'],
            ],
            [
                'name' => 'Precision Mouse MX',
                'slug' => 'precision-mouse-mx',
                'desc' => 'Ergonomic wireless mouse with 8K DPI optical sensor and 70-day battery life.',
                'short' => '8K DPI · 70-day battery · Ergonomic',
                'price' => 149, 'sale' => null,
                'sku' => 'PMM-001', 'featured' => false, 'new' => false,
                'specs' => ['sensor' => '8K DPI Optical', 'connection' => 'USB-C / Bluetooth 5.2', 'battery' => '70 days', 'buttons' => '8 programmable'],
            ],
            [
                'name' => 'Noise-Canceling Headphones',
                'slug' => 'noise-canceling-headphones',
                'desc' => 'Premium ANC headphones with adaptive noise cancellation, 40-hour battery, and Hi-Res Audio.',
                'short' => 'Adaptive ANC · 40hr · Hi-Res Audio',
                'price' => 349, 'sale' => null,
                'sku' => 'NCH-001', 'featured' => true, 'new' => true,
                'specs' => ['driver' => '40mm custom dynamic', 'anc' => 'Adaptive ANC', 'battery' => '40 hours', 'codec' => 'LDAC, AAC, SBC', 'weight' => '250g'],
            ],
        ],
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCategories($manager);
        $this->loadProducts($manager);
        $this->loadFlashDeals($manager);
        $this->loadUsers($manager);
        $this->loadReviews($manager);
        $this->loadNewsletterSubscriptions($manager);
        $this->loadCarts($manager);
        $this->loadProductComparisons($manager);

        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setSlug($data['slug']);
            $category->setDescription($data['desc']);
            $category->setDisplayOrder($data['order']);
            $category->setImageUrl('https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800');
            $manager->persist($category);
            $this->addReference('category-' . $data['slug'], $category);
        }
    }

    private function loadProducts(ObjectManager $manager): void
    {
        $gamingImgs = [
            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800',
            'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=800',
            'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800',
        ];
        $businessImgs = [
            'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800',
            'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=800',
        ];
        $imgIdx = 0;

        foreach (self::PRODUCTS as $catSlug => $products) {
            $category = $this->getReference('category-' . $catSlug, Category::class);

            foreach ($products as $data) {
                $product = new Product();
                $product->setName($data['name']);
                $product->setSlug($data['slug']);
                $product->setDescription($data['desc']);
                $product->setShortDescription($data['short']);
                $product->setPrice($data['price']);
                $product->setSalePrice($data['sale']);
                $product->setSku($data['sku']);
                $product->setStock(random_int(5, 50));
                $product->setFeatured($data['featured']);
                $product->setNewArrival($data['new']);
                $product->setSpecs($data['specs']);

                $imgUrl = $catSlug === 'gaming' ? $gamingImgs[$imgIdx % count($gamingImgs)] : $businessImgs[$imgIdx % count($businessImgs)];
                $product->setImageUrl($imgUrl);
                $product->setGalleryImages([$imgUrl]);
                $imgIdx++;

                $product->addCategory($category);
                $manager->persist($product);
                $this->addReference('product-' . $data['slug'], $product);
            }
        }
    }

    private function loadFlashDeals(ObjectManager $manager): void
    {
        $dealProducts = ['lumina-blade-14', 'titan-x-pro', 'vector-15s', 'nexus-g1'];
        $discounts = [25, 15, 30, 10];
        $maxQtys = [60, 50, 150, 20];

        foreach ($dealProducts as $i => $slug) {
            $product = $this->getReference('product-' . $slug, Product::class);

            $deal = new FlashDeal();
            $deal->setProduct($product);
            $deal->setDiscountPercent($discounts[$i]);
            $deal->setStartAt(new \DateTimeImmutable('-1 day'));
            $deal->setEndAt(new \DateTimeImmutable('+3 days'));
            $deal->setQuantitySold(random_int(5, $maxQtys[$i] - 5));
            $deal->setMaxQuantity($maxQtys[$i]);
            $deal->setActive(true);
            $manager->persist($deal);
        }
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@luminaelite.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Lumina');
        $admin->setVerified(true);
        $manager->persist($admin);
        $this->addReference('user-admin', $admin);

        $user = new User();
        $user->setEmail('john@example.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user1234'));
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setVerified(true);
        $manager->persist($user);
        $this->addReference('user-john', $user);

        $student = new User();
        $student->setEmail('alice@university.edu');
        $student->setPassword($this->passwordHasher->hashPassword($student, 'student123'));
        $student->setFirstName('Alice');
        $student->setLastName('Smith');
        $student->setVerified(true);
        $student->setStudentVerified(true);
        $manager->persist($student);
        $this->addReference('user-alice', $student);
    }

    private function loadReviews(ObjectManager $manager): void
    {
        $reviewData = [
            ['product' => 'lumina-blade-14', 'user' => 'user-john', 'rating' => 5, 'title' => 'Absolute beast!', 'comment' => 'Best gaming laptop I have ever owned. The 165Hz OLED display is stunning and the RTX 4070 handles everything I throw at it.'],
            ['product' => 'lumina-blade-14', 'user' => 'user-alice', 'rating' => 4, 'title' => 'Almost perfect', 'comment' => 'Incredible performance and build quality. Battery life could be better when gaming but that is expected.'],
            ['product' => 'titan-x-pro', 'user' => 'user-john', 'rating' => 5, 'title' => 'Desktop replacement', 'comment' => 'RTX 5090 in a laptop is insane. Runs Cyberpunk at 4K ultra smooth. The vapor chamber cooling is no joke.'],
            ['product' => 'elitebook-ultra-g3', 'user' => 'user-admin', 'rating' => 5, 'title' => 'Best business laptop', 'comment' => 'Lightweight, powerful, and the build quality is exceptional. The OLED screen is perfect for long workdays.'],
            ['product' => 'thinkpad-x1-carbon-gen12', 'user' => 'user-john', 'rating' => 4, 'title' => 'Classic ThinkPad', 'comment' => 'The keyboard is still the best in class. Battery life is fantastic. Wish it had a higher resolution display option.'],
            ['product' => 'precision-7780', 'user' => 'user-admin', 'rating' => 5, 'title' => 'Workstation powerhouse', 'comment' => 'Rendering times dropped by 60% compared to my old setup. ISV certification means SolidWorks runs flawlessly.'],
            ['product' => 'lumina-book-14', 'user' => 'user-alice', 'rating' => 5, 'title' => 'Perfect for university', 'comment' => 'Light, fast, and the battery lasts all day. Great value for students.'],
        ];

        foreach ($reviewData as $data) {
            $review = new Review();
            $review->setProduct($this->getReference('product-' . $data['product'], Product::class));
            $review->setUser($this->getReference($data['user'], User::class));
            $review->setRating($data['rating']);
            $review->setTitle($data['title']);
            $review->setComment($data['comment']);
            $review->setApproved(true);
            $review->setCreatedAt(new \DateTimeImmutable('-' . random_int(1, 30) . ' days'));
            $manager->persist($review);
        }
    }

    private function loadNewsletterSubscriptions(ObjectManager $manager): void
    {
        $emails = ['john@example.com', 'alice@university.edu', 'sarah@company.com', 'mike@startup.io'];
        foreach ($emails as $email) {
            $sub = new NewsletterSubscription();
            $sub->setEmail($email);
            $sub->setVerified(true);
            $manager->persist($sub);
        }
    }

    private function loadCarts(ObjectManager $manager): void
    {
        $cart = new Cart();
        $cart->setSessionId('demo-session-001');

        $item1 = new CartItem();
        $item1->setCart($cart);
        $item1->setProduct($this->getReference('product-lumina-blade-14', Product::class));
        $item1->setQuantity(1);
        $item1->setUnitPrice(1899);

        $item2 = new CartItem();
        $item2->setCart($cart);
        $item2->setProduct($this->getReference('product-elite-mechanical-kb', Product::class));
        $item2->setQuantity(2);
        $item2->setUnitPrice(199);

        $manager->persist($cart);
        $manager->persist($item1);
        $manager->persist($item2);
    }

    private function loadProductComparisons(ObjectManager $manager): void
    {
        $comparison = new ProductComparison();
        $comparison->setSessionId('demo-session-001');
        $comparison->addProduct($this->getReference('product-lumina-blade-14', Product::class));
        $comparison->addProduct($this->getReference('product-titan-x-pro', Product::class));
        $manager->persist($comparison);
    }
}
