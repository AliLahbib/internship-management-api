<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\InternshipRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin')]
class AdminController extends AbstractController
{


    #[Route('', name: 'admin_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();
        $data = $this->serializer->serialize($admins, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'admin_by_id', methods: ['GET'])]

    public function get(int $id): JsonResponse
    {
        $admin = $this->getDoctrine()->getRepository(Admin::class)->find($id);

        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], 404);
        }

        $data = $this->serializer->serialize($admin, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('', name: 'create_admin', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Initialiser un nouvel administrateur
        $admin = new Admin();
        $admin->setEmail($data['email'] ?? null);
        $admin->setDepartment($data['department'] ?? null);

        try {
            $createdAdmin = $this->userService->createUser($data); // Utilise le service générique
            $responseData = $this->serializer->serialize($createdAdmin, 'json', ['groups' => 'user:read']);
            return new JsonResponse($responseData, 201, [], true);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }


    #[Route('/requests', name: 'admin_list_requests', methods: ['GET'])]
    public function listRequests(EntityManagerInterface $entityManager): JsonResponse
    {
        $requests = $entityManager->getRepository(InternshipRequest::class)->findAll();
        return $this->json($requests);
    }

    #[Route('/requests/{id}/status', name: 'update_request_status', methods: ['PATCH'])]
    public function updateRequestStatus(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $internshipRequest = $entityManager->getRepository(InternshipRequest::class)->find($id);
        $data = json_decode($request->getContent(), true);
        
        $internshipRequest->setStatus($data['status']);
        $entityManager->flush();
        
        return $this->json($internshipRequest);
    }
} 