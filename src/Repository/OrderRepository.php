<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByUser(int $userId): array
    {
        return $this->findBy(['user' => $userId], ['createdAt' => 'DESC']);
    }

    public function findByStatus(OrderStatus $status): array
    {
        return $this->findBy(['status' => $status], ['createdAt' => 'DESC']);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->findOneBy(['orderNumber' => $orderNumber]);
    }

    public function findRecent(int $limit = 10): array
    {
        return $this->findBy([], ['createdAt' => 'DESC'], $limit);
    }

    public function getOrderStats(): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o.status', 'COUNT(o.id) as order_count', 'SUM(o.totalAmount) as revenue')
            ->groupBy('o.status');

        $results = $qb->getQuery()->getResult();

        $stats = [];
        foreach ($results as $row) {
            $status = $row['status'] instanceof OrderStatus ? $row['status']->value : $row['status'];
            $stats[$status] = [
                'count' => (int) $row['order_count'],
                'revenue' => (float) ($row['revenue'] ?? 0),
            ];
        }

        foreach (OrderStatus::cases() as $status) {
            if (!isset($stats[$status->value])) {
                $stats[$status->value] = ['count' => 0, 'revenue' => 0.0];
            }
        }

        return $stats;
    }
}
