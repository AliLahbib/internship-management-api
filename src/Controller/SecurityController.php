<?php

namespace App\Controller;

use App\Entity\User;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


class SecurityController extends AbstractController
{
    #[Route('/api/secure', name: 'api_secure', methods: ['GET'])]
    public function secure(): JsonResponse
    {
        return $this->json(['message' => 'Access Granted!']);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(UserInterface $user): JsonResponse
    {
        return $this->json([
            'message' => '✅ Authentification réussie',
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): void
    {
        throw new \Exception('Ne sera jamais atteint - Symfony gère la déconnexion via le firewall.');
    }



}