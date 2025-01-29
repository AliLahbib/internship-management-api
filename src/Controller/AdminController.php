<?php

namespace App\Controller;

use App\Entity\InternshipRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin')]
class AdminController extends AbstractController
{
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