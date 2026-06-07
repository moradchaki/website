<?php

namespace App\Twig\Components;

use App\Entity\Product;
use App\Repository\CartRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class ProductCard
{
    public Product $product;
    public string $variant = 'default';
    public bool $isLiked = false;
    public int $cartItemCount = 0;

    public function __construct(
        private Security $security,
        private CartRepository $cartRepo,
        private RequestStack $requestStack,
    ) {
    }

    public function mount(Product $product): void
    {
        $this->product = $product;

        $user = $this->security->getUser();
        if ($user) {
            $this->isLiked = $this->product->isLikedByUser($user);
        }

        $sessionId = $this->requestStack->getSession()->getId();
        $cart = $this->cartRepo->findBySessionId($sessionId);
        if ($cart) {
            $this->cartItemCount = $cart->getItemCount();
        }
    }
}
