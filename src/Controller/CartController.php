<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('', name: 'cart_index')]
    public function index(Request $request, CartRepository $cartRepo): Response
    {
        $sessionId = $request->getSession()->getId();
        $cart = $cartRepo->findBySessionId($sessionId);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request, CartRepository $cartRepo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $sessionId = $request->getSession()->getId();
        $cart = $cartRepo->findBySessionId($sessionId);

        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            $em->persist($cart);
        }

        $existingItem = null;
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()?->getId() === $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + 1);
        } else {
            $item = new CartItem();
            $item->setCart($cart);
            $item->setProduct($product);
            $item->setUnitPrice($product->getEffectivePrice());
            $item->setQuantity(1);
            $em->persist($item);
        }

        $em->flush();

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(CartItem $item, EntityManagerInterface $em): Response
    {
        $em->remove($item);
        $em->flush();
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(CartItem $item, Request $request, EntityManagerInterface $em): Response
    {
        $qty = (int) $request->request->get('quantity', 1);
        if ($qty < 1) {
            $em->remove($item);
        } else {
            $item->setQuantity($qty);
        }
        $em->flush();
        return $this->redirectToRoute('cart_index');
    }
}
