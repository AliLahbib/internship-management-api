<?php

namespace App\Services;

use App\Entity\Admin;
use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use  Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserServices
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function getAllUsers(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function getUserById(int $id): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function createUser(array $data): ?User
    {

        if ($data['type'] === 'student') {
            $user = new Student();
            $user->setProgram($data["program"]??null);
            $user->setStudyYear($data["studyYear"]??null);
            $user->setIdentityCardNumber($data["identity_card_number"] ?? null);
        } elseif ($data['type'] === 'admin') {
            $user = new Admin();
            $user->setDepartment($data['department'] ?? null);
        }

        $user->setEmail($data['email'] ?? null);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password'] ?? ''));


        $user->setFirstName($data['firstName'] ?? null);
        $user->setLastName($data['lastName'] ?? null);
        $user->setPhone($data['phone'] ?? null);
        $user->setRoles($data['roles'] ?? []);


        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function deleteUserById(int $id): void
    {
        $user = $this->getUserById($id);

        if (!$user) {
            throw new \InvalidArgumentException("Utilisateur non trouvé.");
        }

        $this->deleteUser($user);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(int $id, array $data): ?User
    {
        $user = $this->getUserById($id);

        if (!$user) {
            throw new \InvalidArgumentException("Utilisateur non trouvé.");
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (!empty($data['password'])) {
            $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));
        }

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        if (isset($data['phone'])) {
            $user->setPhone($data['phone']);
        }

        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }


        if ($user instanceof Admin && isset($data['department'])) {
            $user->setDepartment($data['department']);
        }

//        // Validation des données
//        $this->validateUser($user);

        $this->entityManager->flush();

        return $user;
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * Formater les erreurs de validation en texte
     */
    private function formatValidationErrors($errors): string
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
        }
        return implode(', ', $errorMessages);
    }
}