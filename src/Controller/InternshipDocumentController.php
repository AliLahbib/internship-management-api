<?php

namespace App\Controller;

use App\Entity\InternshipRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/documents')]
class InternshipDocumentController extends AbstractController
{
    #[Route('/convention/{id}', name: 'generate_convention', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function generateConvention(InternshipRequest $request): Response
    {
        // TODO: Implémenter la génération de la convention
        // Utiliser un service pour générer le PDF
        
        return new BinaryFileResponse(
            'path/to/generated/convention.pdf',
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }

    #[Route('/attestation/{id}', name: 'generate_attestation', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function generateAttestation(InternshipRequest $request): Response
    {
        // TODO: Implémenter la génération de l'attestation
        
        return new BinaryFileResponse(
            'path/to/generated/attestation.pdf',
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }
} 