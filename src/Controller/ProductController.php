<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductLikeRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('', name: 'product_index')]
    public function index(ProductRepository $productRepo, CategoryRepository $categoryRepo, Request $request): Response
    {
        $products = $productRepo->findByFilters(
            maxPrice: $request->query->get('maxPrice'),
            categorySlug: $request->query->get('category'),
            sort: $request->query->get('sort'),
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categoryRepo->findActiveOrdered(),
            'currentCategory' => $request->query->get('category'),
            'currentSort' => $request->query->get('sort'),
        ]);
    }

    #[Route('/search', name: 'product_search')]
    public function search(ProductRepository $productRepo, Request $request): Response
    {
        $query = $request->query->get('q', '');
        $products = $query ? $productRepo->search($query) : [];

        return $this->render('product/search.html.twig', [
            'products' => $products,
            'query' => $query,
        ]);
    }

    #[Route('/wishlist', name: 'product_wishlist')]
    public function wishlist(ProductLikeRepository $likeRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $likes = $likeRepo->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);
        $products = array_map(fn($like) => $like->getProduct(), $likes);

        return $this->render('product/wishlist.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/{slug}', name: 'product_show')]
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Product $product, EntityManagerInterface $em): Response
    {
        $product->setViewsCount($product->getViewsCount() + 1);
        $em->flush();

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/like', name: 'product_like', methods: ['POST'])]
    public function like(Product $product, EntityManagerInterface $em, ProductLikeRepository $likeRepo): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $existing = $likeRepo->findByUserAndProduct($this->getUser(), $product);

        if ($existing) {
            $em->remove($existing);
            $product->setLikesCount($product->getLikesCount() - 1);
            $liked = false;
        } else {
            $like = new \App\Entity\ProductLike();
            $like->setUser($this->getUser());
            $like->setProduct($product);
            $em->persist($like);
            $product->setLikesCount($product->getLikesCount() + 1);
            $liked = true;
        }

        $em->flush();

        return $this->json([
            'liked' => $liked,
            'likesCount' => $product->getLikesCount(),
        ]);
    }
}
