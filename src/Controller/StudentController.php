<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\InternshipRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/students')]
class StudentController extends AbstractController
{
    #[Route('/profile', name: 'student_profile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        /** @var Student $student */
        $student = $this->getUser();
        return $this->json($student);
    }

    #[Route('/profile', name: 'update_profile', methods: ['PUT'])]
    public function updateProfile(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        /** @var Student $student */
        $student = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['phone'])) {
            $student->setPhone($data['phone']);
        }
        if (isset($data['program'])) {
            $student->setProgram($data['program']);
        }
        if (isset($data['studyYear'])) {
            $student->setStudyYear($data['studyYear']);
        }

        $entityManager->flush();

        return $this->json($student);
    }

    #[Route('/requests', name: 'student_requests', methods: ['GET'])]
    public function getMyRequests(EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var Student $student */
        $student = $this->getUser();
        
        $requests = $entityManager->getRepository(InternshipRequest::class)
            ->findBy(['student' => $student], ['createdAt' => 'DESC']);
            
        return $this->json($requests);
    }

    #[Route('/requests', name: 'submit_request', methods: ['POST'])]
    public function submitRequest(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $internshipRequest = new InternshipRequest();
        $internshipRequest->setStudent($this->getUser());
        // Set other properties from $data
        
        $entityManager->persist($internshipRequest);
        $entityManager->flush();
        
        return $this->json($internshipRequest, 201);
    }
} 