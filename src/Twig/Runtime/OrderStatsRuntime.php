<?php

namespace App\Twig\Runtime;

use App\Repository\OrderRepository;
use Twig\Extension\RuntimeExtensionInterface;

class OrderStatsRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository
    ) {
    }

    public function getOrderStats(): array
    {
        return $this->orderRepository->getOrderStats();
    }
}
