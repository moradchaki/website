<?php

namespace App\Repository;

use App\Entity\ProductComparison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductComparisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductComparison::class);
    }

    public function findBySessionId(string $sessionId): ?ProductComparison
    {
        return $this->findOneBy(['sessionId' => $sessionId]);
    }
}
