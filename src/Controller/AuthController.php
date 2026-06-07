<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'auth_register')]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setFirstName($request->request->get('firstName'));
            $user->setLastName($request->request->get('lastName'));
            $user->setPassword($hasher->hashPassword($user, $request->request->get('password')));
            $user->setVerified(true);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('auth_login');
        }

        return $this->render('auth/register.html.twig');
    }

    #[Route('/login', name: 'auth_login')]
    public function login(AuthenticationUtils $auth): Response
    {
        return $this->render('auth/login.html.twig', [
            'lastEmail' => $auth->getLastUsername(),
            'error' => $auth->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'auth_logout')]
    public function logout(): void
    {
    }

    #[Route('/profile', name: 'auth_profile')]
    public function profile(OrderRepository $orderRepo): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('auth_login');
        }

        return $this->render('auth/profile.html.twig', [
            'user' => $user,
            'orders' => $orderRepo->findByUser($user->getId()),
        ]);
    }
}
