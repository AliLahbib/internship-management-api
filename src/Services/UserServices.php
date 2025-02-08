<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use  Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserServices
{
    private $entityManager;
    private $passwordEncoder;
    private $validator;

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
        $user = new User();
        $user->setEmail($data['email'] ?? null);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password'] ?? ''));
        $user->setFirstName($data['firstName'] ?? null);
        $user->setLastName($data['lastName'] ?? null);
        $user->setPhone($data['phone'] ?? null);
        $user->setRoles($data['roles'] ?? []);

        // Valider les données
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException($this->formatValidationErrors($errors));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(User $user, array $data): ?User
    {
        $user->setEmail($data['email'] ?? $user->getEmail());
        if (!empty($data['password'])) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
        }
        $user->setFirstName($data['firstName'] ?? $user->getFirstName());
        $user->setLastName($data['lastName'] ?? $user->getLastName());
        $user->setPhone($data['phone'] ?? $user->getPhone());
        $user->setRoles($data['roles'] ?? $user->getRoles());

        // Valider les données
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException($this->formatValidationErrors($errors));
        }

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