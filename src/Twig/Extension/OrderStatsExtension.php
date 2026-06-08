<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\OrderStatsRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OrderStatsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('order_stats', [OrderStatsRuntime::class, 'getOrderStats']),
        ];
    }
}
