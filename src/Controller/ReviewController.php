<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products/{slug}/reviews')]
class ReviewController extends AbstractController
{
    #[Route('', name: 'review_index')]
    public function index(#[MapEntity(mapping: ['slug' => 'slug'])] Product $product, ReviewRepository $reviewRepo): Response
    {
        return $this->render('review/index.html.twig', [
            'product' => $product,
            'reviews' => $reviewRepo->findApprovedByProduct($product->getId()),
        ]);
    }

    #[Route('/new', name: 'review_new', methods: ['POST'])]
    public function new(#[MapEntity(mapping: ['slug' => 'slug'])] Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('auth_login');
        }

        $review = new Review();
        $review->setProduct($product);
        $review->setUser($user);
        $review->setRating((int) $request->request->get('rating', 5));
        $review->setTitle($request->request->get('title', ''));
        $review->setComment($request->request->get('comment', ''));
        $em->persist($review);
        $em->flush();

        return $this->redirectToRoute('review_index', ['slug' => $product->getSlug()]);
    }
}
