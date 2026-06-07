<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class SidebarLink
{
    public string $href = '#';
    public string $label = '';
    public string $icon = 'circle';
    public bool $active = false;
}
