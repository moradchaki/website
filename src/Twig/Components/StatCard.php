<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class StatCard
{
    public string $label = '';
    public string $value = '0';
    public string $icon = 'chart';
    public string $color = 'primary';
}
