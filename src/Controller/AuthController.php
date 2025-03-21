<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api')]
class AuthController extends AbstractController
{
    #[Route('/register/student', name: 'register_student', methods: ['POST'])]
    public function registerStudent(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $student = new Student();
        $student->setEmail($data['email']);
        $student->setPassword(
            $passwordHasher->hashPassword($student, $data['password'])
        );
        $student->setFirstName($data['firstName']);
        $student->setLastName($data['lastName']);
        $student->setPhone($data['phone']);
        $student->setIdentityCardNumber($data['identity_card_number']);
        $student->setProgram($data['program']);
        $student->setStudyYear($data['studyYear']);

        $entityManager->persist($student);
        $entityManager->flush();

        return $this->json([
            'message' => 'Registered Successfully'
        ], Response::HTTP_CREATED);
    }
    #[Route('/user', name: 'api_user', methods: ['GET'])]
    public function getUserData(): JsonResponse
    {
        $user = $this->getUser(); // Symfony récupère l'utilisateur

        if (!$user) {
            return $this->json([
                'message' => 'Utilisateur non authentifié',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'roles' => $user->getRoles(),
        ]);
    }


} 