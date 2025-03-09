<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\InternshipRequest;
use App\Services\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/requests')]
class InternshipRequestController extends AbstractController
{
    public function __construct(
        private ValidationService $validationService
    ) {}

    #[Route('', name: 'create_request', methods: ['POST'])]
//    #[IsGranted('ROLE_STUDENT')]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $internshipRequest = new InternshipRequest();
            $internshipRequest->setStudent($this->getUser());
            $internshipRequest->setType($data['type']);
            $internshipRequest->setStartDate(new \DateTime($data['startDate']));
            $internshipRequest->setEndDate(new \DateTime($data['endDate']));
            $internshipRequest->setDepartment($data['department']);
            $internshipRequest->setCompany($data['company_id'] ? $entityManager->getReference(Company::class, $data['company_id']) : null);

//            $this->validationService->validate($internshipRequest);

            $entityManager->persist($internshipRequest);
            $entityManager->flush();

            return $this->json($internshipRequest, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/status', name: 'update_request_status', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateStatus(
        InternshipRequest $internshipRequest,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $internshipRequest->setStatus($data['status']);
            $this->validationService->validate($internshipRequest);

            $entityManager->flush();

            return $this->json($internshipRequest);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}