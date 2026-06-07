<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findApprovedByProduct(int $productId): array
    {
        return $this->findBy(
            ['product' => $productId, 'isApproved' => true],
            ['createdAt' => 'DESC']
        );
    }

    public function findPending(): array
    {
        return $this->findBy(['isApproved' => false], ['createdAt' => 'DESC']);
    }

    public function getAverageRating(int $productId): ?float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->where('r.product = :productId')
            ->andWhere('r.isApproved = true')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
