<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

class SecurityController extends AbstractController
{
    #[Route('/api/secure', name: 'api_secure', methods: ['GET'])]
    public function secure(): JsonResponse
    {
        return $this->json(['message' => 'Access Granted!']);
    }


    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        #[CurrentUser] ?User $user,
        JWTTokenManagerInterface $jwtManager,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ): JsonResponse {


        if (null === $user) {
            return $this->json([
                'message' => 'Identifiants invalides',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $token = $jwtManager->create($user);

        return $this->json([
            'token' => $token,
            'user' => json_decode($serializer->serialize($user, 'json', [
                'groups' => ['user:read']
            ]))
        ]);
    }
}