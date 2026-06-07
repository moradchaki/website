<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderStatus;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/orders')]
class OrderController extends AbstractController
{
    #[Route('', name: 'order_index')]
    public function index(OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();
        $orders = $user ? $orderRepo->findByUser($user->getId()) : [];

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/checkout', name: 'order_checkout', methods: ['POST'])]
    public function checkout(Request $request, CartRepository $cartRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('auth_login');
        }

        $sessionId = $request->getSession()->getId();
        $cart = $cartRepo->findBySessionId($sessionId);

        if (!$cart || $cart->getItems()->isEmpty()) {
            return $this->redirectToRoute('cart_index');
        }

        $order = new Order();
        $order->setOrderNumber('ORD-' . strtoupper(uniqid()));
        $order->setUser($user);
        $order->setStatus(OrderStatus::Pending);

        $subtotal = 0;
        foreach ($cart->getItems() as $cartItem) {
            $item = new OrderItem();
            $item->setOrder($order);
            $item->setProduct($cartItem->getProduct());
            $item->setQuantity($cartItem->getQuantity());
            $item->setUnitPrice($cartItem->getUnitPrice());
            $em->persist($item);
            $subtotal += $item->getTotalPrice();
        }

        $order->setSubtotal($subtotal);
        $order->setTaxAmount(round($subtotal * 0.08, 2));
        $order->setShippingAmount(0);
        $order->setTotalAmount($order->getSubtotal() + $order->getTaxAmount() + $order->getShippingAmount());
        $em->persist($order);

        foreach ($cart->getItems()->toArray() as $item) {
            $em->remove($item);
        }
        $em->remove($cart);
        $em->flush();

        $this->addFlash('success', 'Order ' . $order->getOrderNumber() . ' placed successfully!');
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/{id}', name: 'order_show')]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
