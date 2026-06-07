<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/gaming', name: 'gaming')]
    public function gaming(ProductRepository $productRepo): Response
    {
        return $this->render('category/gaming.html.twig', [
            'products' => $productRepo->findByCategorySlug('gaming'),
        ]);
    }

    #[Route('/business', name: 'business')]
    public function business(ProductRepository $productRepo): Response
    {
        return $this->render('category/business.html.twig', [
            'products' => $productRepo->findByCategorySlug('business'),
        ]);
    }

    #[Route('/student', name: 'student')]
    public function student(ProductRepository $productRepo): Response
    {
        return $this->render('category/student.html.twig', [
            'products' => $productRepo->findByCategorySlug('student'),
        ]);
    }

    #[Route('/workstations', name: 'workstations')]
    public function workstations(ProductRepository $productRepo): Response
    {
        return $this->render('category/workstations.html.twig', [
            'products' => $productRepo->findByCategorySlug('workstations'),
        ]);
    }

    #[Route('/accessories', name: 'accessories')]
    public function accessories(ProductRepository $productRepo): Response
    {
        return $this->render('category/accessories.html.twig', [
            'products' => $productRepo->findByCategorySlug('accessories'),
        ]);
    }
}
