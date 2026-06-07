<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductLike;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductLikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductLike::class);
    }

    public function findByUserAndProduct(User $user, Product $product): ?ProductLike
    {
        return $this->findOneBy(['user' => $user, 'product' => $product]);
    }

    public function countByProduct(Product $product): int
    {
        return $this->count(['product' => $product]);
    }
}
