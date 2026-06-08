<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\FlashDeal;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Review;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use App\Repository\FlashDealRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(ProductRepository $productRepo, OrderRepository $orderRepo, ReviewRepository $reviewRepo, CategoryRepository $categoryRepo, FlashDealRepository $flashDealRepo): Response
    {
        $products = $productRepo->findAll();
        $totalRevenue = 0;
        foreach ($orderRepo->findAll() as $order) {
            $totalRevenue += $order->getTotalAmount();
        }

        return $this->render('admin/index.html.twig', [
            'stats' => [
                'total_products' => count($products),
                'total_categories' => count($categoryRepo->findAll()),
                'total_orders' => count($orderRepo->findAll()),
                'total_reviews' => count($reviewRepo->findAll()),
                'total_revenue' => $totalRevenue,
                'active_deals' => count($flashDealRepo->findActive()),
                'low_stock' => count(array_filter($products, fn($p) => $p->getStock() < 10)),
            ],
            'category_stats' => $productRepo->getCategoryStats(),
            'recent_products' => $productRepo->findBy([], ['createdAt' => 'DESC'], 5),
            'recent_orders' => $orderRepo->findRecent(5),
            'pending_reviews' => $reviewRepo->findPending(),
        ]);
    }

    #[Route('/products', name: 'admin_product_index')]
    public function productIndex(ProductRepository $productRepo, CategoryRepository $categoryRepo): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepo->findBy([], ['createdAt' => 'DESC']),
            'categories' => $categoryRepo->findAll(),
        ]);
    }

    #[Route('/products/new', name: 'admin_product_new')]
    public function productNew(Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepo, SluggerInterface $slugger): Response
    {
        $categories = $categoryRepo->findAll();

        if ($request->isMethod('POST')) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setSlug($slugger->slug($product->getName())->lower()->toString());
            $product->setDescription($request->request->get('description'));
            $product->setShortDescription($request->request->get('shortDescription'));
            $product->setPrice((float) $request->request->get('price'));
            $salePrice = $request->request->get('salePrice');
            if ($salePrice !== '') {
                $product->setSalePrice((float) $salePrice);
            }
            $product->setSku($request->request->get('sku'));
            $product->setStock((int) $request->request->get('stock'));
            $product->setImageUrl($request->request->get('imageUrl'));
            $product->setFeatured($request->request->has('isFeatured'));
            $product->setNewArrival($request->request->has('isNewArrival'));

            $categoryIds = $request->request->all('categories');
            foreach ($categoryIds as $cid) {
                $cat = $categoryRepo->find($cid);
                if ($cat) {
                    $product->addCategory($cat);
                }
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product created successfully.');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/form.html.twig', [
            'product' => null,
            'categories' => $categories,
        ]);
    }

    #[Route('/products/{id}/edit', name: 'admin_product_edit')]
    public function productEdit(Product $product, Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepo, SluggerInterface $slugger): Response
    {
        $categories = $categoryRepo->findAll();

        if ($request->isMethod('POST')) {
            $product->setName($request->request->get('name'));
            $product->setSlug($slugger->slug($product->getName())->lower()->toString());
            $product->setDescription($request->request->get('description'));
            $product->setShortDescription($request->request->get('shortDescription'));
            $product->setPrice((float) $request->request->get('price'));
            $salePrice = $request->request->get('salePrice');
            if ($salePrice !== '') {
                $product->setSalePrice((float) $salePrice);
            } else {
                $product->setSalePrice(null);
            }
            $product->setSku($request->request->get('sku'));
            $product->setStock((int) $request->request->get('stock'));
            $product->setImageUrl($request->request->get('imageUrl'));
            $product->setFeatured($request->request->has('isFeatured'));
            $product->setNewArrival($request->request->has('isNewArrival'));

            foreach ($product->getCategories()->toArray() as $cat) {
                $product->removeCategory($cat);
            }
            foreach ($request->request->all('categories') as $cid) {
                $cat = $categoryRepo->find($cid);
                if ($cat) {
                    $product->addCategory($cat);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Product updated successfully.');
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('admin/product/form.html.twig', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    #[Route('/products/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function productDelete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'Product deleted successfully.');
        return $this->redirectToRoute('admin_product_index');
    }

    #[Route('/orders', name: 'admin_order_index')]
    public function orderIndex(OrderRepository $orderRepo): Response
    {
        return $this->render('admin/order/index.html.twig', [
            'orders' => $orderRepo->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/orders/{id}', name: 'admin_order_show')]
    public function orderShow(Order $order): Response
    {
        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/transactions', name: 'admin_transaction_index')]
    public function transactionIndex(OrderRepository $orderRepo): Response
    {
        $orders = $orderRepo->findBy([], ['createdAt' => 'DESC']);
        $stats = $orderRepo->getOrderStats();

        $totalRevenue = 0;
        $totalOrders = 0;
        foreach ($stats as $s) {
            $totalOrders += $s['count'];
            $totalRevenue += $s['revenue'];
        }

        return $this->render('admin/transaction/index.html.twig', [
            'orders' => $orders,
            'stats' => $stats,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
        ]);
    }
}
