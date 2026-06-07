<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductComparison;
use App\Repository\ProductComparisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/compare')]
class ComparisonController extends AbstractController
{
    #[Route('', name: 'comparison_index')]
    public function index(Request $request, ProductComparisonRepository $repo): Response
    {
        $sessionId = $request->getSession()->getId();
        $comparison = $repo->findBySessionId($sessionId);

        return $this->render('comparison/index.html.twig', [
            'comparison' => $comparison,
        ]);
    }

    #[Route('/add/{id}', name: 'comparison_add', methods: ['POST'])]
    public function add(Product $product, Request $request, ProductComparisonRepository $repo, EntityManagerInterface $em): Response
    {
        $sessionId = $request->getSession()->getId();
        $comparison = $repo->findBySessionId($sessionId);

        if (!$comparison) {
            $comparison = new ProductComparison();
            $comparison->setSessionId($sessionId);
            $em->persist($comparison);
        }

        if (!$comparison->getProducts()->contains($product)) {
            if ($comparison->getProducts()->count() >= 4) {
                $this->addFlash('error', 'You can compare up to 4 products at a time.');
                return $this->redirectToRoute('comparison_index');
            }
            $comparison->addProduct($product);
            $em->flush();
        }

        return $this->redirectToRoute('comparison_index');
    }

    #[Route('/remove/{id}', name: 'comparison_remove', methods: ['POST'])]
    public function remove(Product $product, Request $request, ProductComparisonRepository $repo, EntityManagerInterface $em): Response
    {
        $sessionId = $request->getSession()->getId();
        $comparison = $repo->findBySessionId($sessionId);

        if ($comparison) {
            $comparison->removeProduct($product);
            $em->flush();
        }

        return $this->redirectToRoute('comparison_index');
    }
}
