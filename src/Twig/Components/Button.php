<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{
    public string $label = 'Button';
    public string $variant = 'primary';
    public string $type = 'button';
    public ?string $href = null;
    public ?string $icon = null;
    public string $size = 'md';
    public ?string $action = null;
}
