<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\InternshipRequest;
use App\Services\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/students')]
class StudentController extends AbstractController
{
    private UserServices $userServices;
    private EntityManagerInterface $entityManager;

    public function __construct(UserServices $userServices, EntityManagerInterface $entityManager)
    {
        $this->userServices = $userServices;
        $this->entityManager = $entityManager;
    }

    /**
     * 📌 Récupérer la liste de tous les étudiants
     */
    #[Route('', name: 'student_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $students = $this->entityManager->getRepository(Student::class)->findAll();

        return $this->json($students);
    }

    /**
     * 📌 Récupérer un étudiant par ID
     */
    #[Route('/{id}', name: 'student_by_id', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $student = $this->entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            return new JsonResponse(['message' => 'Student not found'], 404);
        }

        return $this->json($student);
    }

    /**
     * 📌 Créer un étudiant
     */
    #[Route('', name: 'create_student', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $data['type'] = 'student';

        try {
            $createdStudent = $this->userServices->createUser($data);
            return $this->json($createdStudent, 201);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * 📌 Mettre à jour un étudiant
     */
    #[Route('/{id}', name: 'update_student', methods: ['PUT', 'PATCH'])]
    public function updateStudent(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $updatedStudent = $this->userServices->updateUser($id, $data);
            return $this->json($updatedStudent, 200);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * 📌 Supprimer un étudiant
     */
    #[Route('/{id}', name: 'delete_student', methods: ['DELETE'])]
    public function deleteStudent(int $id): JsonResponse
    {
        try {
            $this->userServices->deleteUserById($id);
            return new JsonResponse(['message' => 'Student deleted successfully'], 200);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * 📌 Récupérer le profil d'un étudiant connecté
     */
    #[Route('/profile', name: 'student_profile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        /** @var Student $student */
        $student = $this->getUser();

        if (!$student instanceof Student) {
            return new JsonResponse(['message' => 'Unauthorized'], 403);
        }

        return $this->json($student);
    }

    /**
     * 📌 Mettre à jour le profil d'un étudiant connecté
     */
    #[Route('/profile', name: 'update_profile', methods: ['PUT'])]
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var Student $student */
        $student = $this->getUser();

        if (!$student instanceof Student) {
            return new JsonResponse(['message' => 'Unauthorized'], 403);
        }

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

        $this->entityManager->flush();

        return $this->json($student);
    }



    /**
     * 📌 Récupérer les demandes de stage de l'étudiant connecté
     */
    #[Route('/requests', name: 'student_requests', methods: ['GET'])]
    public function getMyRequests(): JsonResponse
    {
        /** @var Student $student */
        $student = $this->getUser();

        if (!$student instanceof Student) {
            return new JsonResponse(['message' => 'Unauthorized'], 403);
        }

        $requests = $this->entityManager->getRepository(InternshipRequest::class)
            ->findBy(['student' => $student], ['createdAt' => 'DESC']);

        return $this->json($requests);
    }

    /**
     * 📌 Soumettre une nouvelle demande de stage
     */
    #[Route('/requests', name: 'submit_request', methods: ['POST'])]
    public function submitRequest(Request $request): JsonResponse
    {
        /** @var Student $student */
        $student = $this->getUser();

        if (!$student instanceof Student) {
            return new JsonResponse(['message' => 'Unauthorized'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $internshipRequest = new InternshipRequest();
        $internshipRequest->setStudent($student);
        $internshipRequest->setStatus($data['status'] ?? 'pending');
//        $internshipRequest->setCreatedAt(new \DateTime());

        $this->entityManager->persist($internshipRequest);
        $this->entityManager->flush();

        return $this->json($internshipRequest, 201);
    }
}
