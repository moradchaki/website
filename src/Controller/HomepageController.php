<?php

namespace App\Controller;

use App\Repository\FlashDealRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ProductRepository $productRepo, FlashDealRepository $flashDealRepo): Response
    {
        return $this->render('homepage/index.html.twig', [
            'featuredProducts' => $productRepo->findFeatured(),
            'newArrivals' => $productRepo->findNewArrivals(),
            'flashDeals' => $flashDealRepo->findActiveWithProduct(),
            'showcaseProducts' => $productRepo->findBy([], ['createdAt' => 'DESC'], 3),
        ]);
    }
}
