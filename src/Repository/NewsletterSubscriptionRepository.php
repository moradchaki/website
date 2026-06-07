<?php

namespace App\Repository;

use App\Entity\NewsletterSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NewsletterSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterSubscription::class);
    }

    public function findByEmail(string $email): ?NewsletterSubscription
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findActive(): array
    {
        return $this->findBy(['unsubscribedAt' => null]);
    }
}
