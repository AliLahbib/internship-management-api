<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\InternshipRequest;
use App\Services\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/admin')]
class AdminController extends AbstractController
{
    private UserServices  $userServices;

    private EntityManagerInterface $entityManager;

    public function __construct(UserServices $userServices,EntityManagerInterface $entityManager)
    {
        $this->userServices = $userServices;

        $this->entityManager = $entityManager;
    }


    #[Route('', name: 'admin_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $admins = $this->entityManager->getRepository(Admin::class)->findAll();

        $data=$this->json($admins);
        return $data;
    }


    #[Route('/{id}', name: 'admin_by_id', methods: ['GET'])]

    public function get(int $id): JsonResponse
    {
        $admin = $this->entityManager->getRepository(Admin::class)->find($id);

        if (!$admin) {
            return new JsonResponse(['message' => 'Admin not found'], 404);
        }

       return $this->json($admin);
    }

    #[Route('', name: 'create_admin', methods: ['POST'])]
    public function create(Request $request,UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $data['type'] = 'admin';

        try {
            $createdAdmin = $this->userServices->createUser($data); // Utilisation correcte du service
            return $this->json($createdAdmin);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'update_admin', methods: ['PUT', 'PATCH'])]
    public function updateAdmin(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $updatedAdmin = $this->userServices->updateUser($id, $data);
            return $this->json($updatedAdmin, 200);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', name: 'delete_admin', methods: ['DELETE'])]
    public function deleteAdmin(int $id): JsonResponse
    {
        try {
            $this->userServices->deleteUserById($id);
            return new JsonResponse(['message' => 'Admin deleted successfully'], 200);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
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