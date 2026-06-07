<?php

namespace App\Controller;

use App\Entity\NewsletterSubscription;
use App\Repository\NewsletterSubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/newsletter')]
class NewsletterController extends AbstractController
{
    #[Route('/subscribe', name: 'newsletter_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, NewsletterSubscriptionRepository $repo, EntityManagerInterface $em): Response
    {
        $email = $request->request->get('email');

        if (!$email || !filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Please provide a valid email address.');
            return $this->redirect($request->headers->get('referer', '/'));
        }

        $existing = $repo->findByEmail($email);
        if ($existing) {
            if (!$existing->isActive()) {
                $existing->setUnsubscribedAt(null);
                $existing->setVerified(true);
                $em->flush();
                $this->addFlash('success', 'Welcome back! You have been re-subscribed.');
            } else {
                $this->addFlash('info', 'You are already subscribed.');
            }
            return $this->redirect($request->headers->get('referer', '/'));
        }

        $sub = new NewsletterSubscription();
        $sub->setEmail($email);
        $sub->setVerified(true);
        $em->persist($sub);
        $em->flush();

        $this->addFlash('success', 'Successfully subscribed to our newsletter!');
        return $this->redirect($request->headers->get('referer', '/'));
    }

    #[Route('/unsubscribe', name: 'newsletter_unsubscribe', methods: ['POST'])]
    public function unsubscribe(Request $request, NewsletterSubscriptionRepository $repo, EntityManagerInterface $em): Response
    {
        $email = $request->request->get('email');
        $sub = $email ? $repo->findByEmail($email) : null;

        if ($sub) {
            $sub->setUnsubscribedAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'You have been unsubscribed.');
        }

        return $this->redirect($request->headers->get('referer', '/'));
    }
}
