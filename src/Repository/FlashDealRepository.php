<?php

namespace App\Repository;

use App\Entity\FlashDeal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FlashDealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlashDeal::class);
    }

    public function findActive(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('f')
            ->where('f.isActive = true')
            ->andWhere('f.startAt <= :now')
            ->andWhere('f.endAt >= :now')
            ->andWhere('f.maxQuantity IS NULL OR f.quantitySold < f.maxQuantity')
            ->setParameter('now', $now)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveWithProduct(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('f')
            ->innerJoin('f.product', 'p')
            ->addSelect('p')
            ->where('f.isActive = true')
            ->andWhere('f.startAt <= :now')
            ->andWhere('f.endAt >= :now')
            ->andWhere('f.maxQuantity IS NULL OR f.quantitySold < f.maxQuantity')
            ->setParameter('now', $now)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
