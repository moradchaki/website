<?php

namespace App\Command;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderStatus;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:auto-generate-orders', description: 'Generate 15-20 orders daily from random users and products')]
class AutoGenerateOrdersCommand extends Command
{
    private const PAYMENT_METHODS = ['Credit Card', 'PayPal', 'Apple Pay', 'Google Pay', 'Bank Transfer'];

    private const STREETS = [
        '123 Maple Street', '456 Oak Avenue', '789 Pine Road', '321 Elm Drive', '654 Birch Lane',
        '987 Cedar Court', '111 Walnut Way', '222 Cherry Circle', '333 Spruce Street', '444 Ash Avenue',
        '555 Willow Drive', '666 Poplar Lane', '777 Hickory Road', '888 Sycamore Court', '999 Beech Way',
    ];

    private const CITIES = [
        'New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix',
        'Philadelphia', 'San Antonio', 'San Diego', 'Dallas', 'Austin',
        'San Jose', 'Jacksonville', 'Fort Worth', 'Columbus', 'Charlotte',
        'Indianapolis', 'San Francisco', 'Seattle', 'Denver', 'Nashville',
    ];

    private const STATES = [
        'NY', 'CA', 'IL', 'TX', 'AZ', 'PA', 'TX', 'CA', 'TX', 'TX',
        'CA', 'FL', 'TX', 'OH', 'NC', 'IN', 'CA', 'WA', 'CO', 'TN',
    ];

    private const ZIP_CODES = [
        '10001', '90001', '60601', '77001', '85001',
        '19101', '78201', '92101', '75201', '78701',
        '95101', '32201', '76101', '43201', '28201',
        '46201', '94101', '98101', '80201', '37201',
    ];

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepo = $this->em->getRepository(User::class);
        $productRepo = $this->em->getRepository(Product::class);

        $users = $userRepo->findAll();
        $products = $productRepo->findAll();

        if (empty($users)) {
            $output->writeln('<error>No users found. Create users first.</error>');
            return Command::FAILURE;
        }

        if (empty($products)) {
            $output->writeln('<error>No products found. Create products first.</error>');
            return Command::FAILURE;
        }

        $orderCount = random_int(15, 20);
        $today = (new \DateTimeImmutable())->format('Ymd');
        $counter = 1;
        $created = 0;
        $totalRevenue = 0;

        // Filter to laptop-category products only
        $laptopProducts = array_values(array_filter($products, fn(Product $p) => $p->getCategories()->exists(
            fn(int $i, $cat) => in_array($cat->getSlug(), ['gaming', 'business', 'student', 'workstations'], true)
        )));

        if (count($laptopProducts) < 1) {
            $laptopProducts = $products;
        }

        for ($i = 0; $i < $orderCount; $i++) {
            $user = $users[array_rand($users)];
            $numItems = min(random_int(1, 4), count($laptopProducts));

            $orderNumber = sprintf('ORD-%s-%03d', $today, $counter++);
            $statusIndex = random_int(0, 100);
            $status = match (true) {
                $statusIndex < 30 => OrderStatus::Pending,
                $statusIndex < 55 => OrderStatus::Confirmed,
                $statusIndex < 75 => OrderStatus::Processing,
                $statusIndex < 90 => OrderStatus::Shipped,
                default => OrderStatus::Delivered,
            };

            $paymentMethod = self::PAYMENT_METHODS[array_rand(self::PAYMENT_METHODS)];
            $addressIndex = array_rand(self::CITIES);

            $shippingAddress = [
                'street' => self::STREETS[array_rand(self::STREETS)],
                'city' => self::CITIES[$addressIndex],
                'state' => self::STATES[$addressIndex],
                'zip' => self::ZIP_CODES[$addressIndex],
                'country' => 'United States',
                'fullName' => $user->getFullName() ?: 'Valued Customer',
            ];

            $order = new Order();
            $order->setOrderNumber($orderNumber);
            $order->setUser($user);
            $order->setStatus($status);
            $order->setPaymentMethod($paymentMethod);
            $order->setShippingAddress($shippingAddress);
            $order->setNotes(random_int(0, 1) ? 'Auto-generated order from daily cron.' : null);

            $subtotal = 0;
            $productKeys = array_rand($laptopProducts, $numItems);
            if (!is_array($productKeys)) {
                $productKeys = [$productKeys];
            }
            foreach ($productKeys as $key) {
                $product = $laptopProducts[$key];
                $quantity = random_int(1, 2);
                $price = $product->getSalePrice() ?? $product->getPrice();

                $item = new OrderItem();
                $item->setProduct($product);
                $item->setQuantity($quantity);
                $item->setUnitPrice($price);
                $item->setTotalPrice(round($quantity * $price, 2));

                $order->addItem($item);
                $subtotal += $item->getTotalPrice();
            }

            $taxAmount = round($subtotal * 0.08, 2);
            $shippingAmount = $subtotal >= 500 ? 0 : 9.99;
            $totalAmount = round($subtotal + $taxAmount + $shippingAmount, 2);

            $order->setSubtotal($subtotal);
            $order->setTaxAmount($taxAmount);
            $order->setShippingAmount($shippingAmount);
            $order->setTotalAmount($totalAmount);

            $this->em->persist($order);
            $created++;
            $totalRevenue += $totalAmount;

            $itemNames = [];
            foreach ($order->getItems() as $item) {
                $itemNames[] = "{$item->getQuantity()}x {$item->getProduct()->getName()}";
            }
            $output->writeln(
                "<info>CREATED:</info> {$orderNumber} — {$user->getFullName()} — "
                . implode(', ', $itemNames)
                . " — \${$totalAmount} [{$status->value}]"
            );
        }

        $this->em->flush();
        $output->writeln("\n<info>Done! Created {$created} orders (\${$totalRevenue} total revenue).</info>");

        return Command::SUCCESS;
    }
}
