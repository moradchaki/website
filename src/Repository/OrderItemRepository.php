<?php

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function findByOrder(int $orderId): array
    {
        return $this->findBy(['order' => $orderId]);
    }

    public function findByProduct(int $productId): array
    {
        return $this->findBy(['product' => $productId]);
    }
}
