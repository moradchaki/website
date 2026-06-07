<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findFeatured(): array
    {
        return $this->findBy(['isFeatured' => true], ['createdAt' => 'DESC']);
    }

    public function findNewArrivals(): array
    {
        return $this->findBy(['isNewArrival' => true], ['createdAt' => 'DESC']);
    }

    public function findByCategorySlug(string $slug): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :query')
            ->orWhere('p.shortDescription LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(?float $maxPrice, ?string $categorySlug, ?string $sort): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($maxPrice !== null) {
            $qb->andWhere('p.price <= :maxPrice')
               ->setParameter('maxPrice', $maxPrice);
        }

        if ($categorySlug !== null) {
            $qb->innerJoin('p.categories', 'c')
               ->andWhere('c.slug = :slug')
               ->setParameter('slug', $categorySlug);
        }

        match ($sort) {
            'price_asc' => $qb->orderBy('p.price', 'ASC'),
            'price_desc' => $qb->orderBy('p.price', 'DESC'),
            'newest' => $qb->orderBy('p.createdAt', 'DESC'),
            'name' => $qb->orderBy('p.name', 'ASC'),
            default => $qb->orderBy('p.createdAt', 'DESC'),
        };

        return $qb->getQuery()->getResult();
    }

    public function getCategoryStats(): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->select('c.id AS category_id, c.name AS category_name, c.slug AS category_slug, COUNT(p.id) AS product_count, SUM(p.stock) AS total_stock, SUM(p.price) AS total_price')
            ->groupBy('c.id')
            ->orderBy('product_count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $criteria [budget, useCase, performance, portability, display]
     * @return Product[]
     */
    public function findRecommendations(array $criteria): array
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($criteria['budget'])) {
            $qb->andWhere('p.price <= :budget')
               ->setParameter('budget', $criteria['budget']);
        }

        if (!empty($criteria['useCase'])) {
            $qb->innerJoin('p.categories', 'c')
               ->andWhere('c.slug = :useCase')
               ->setParameter('useCase', $criteria['useCase']);
        }

        return $qb->orderBy('p.isFeatured', 'DESC')
            ->addOrderBy('p.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
