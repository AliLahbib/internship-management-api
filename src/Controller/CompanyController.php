<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\InternshipOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/companies')]
class CompanyController extends AbstractController
{
    #[Route('', name: 'create_company', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $company = new Company();
        $company->setName($data['name']);
        $company->setAddress($data['address']);
        $company->setContactEmail($data['contactEmail']);
        $company->setContactPhone($data['contactPhone']);
        
        $entityManager->persist($company);
        $entityManager->flush();
        
        return $this->json($company, 201);
    }

    #[Route('/{id}/offers', name: 'company_offers', methods: ['GET'])]
    public function listOffers(Company $company): JsonResponse
    {
        return $this->json($company->getInternshipOffers());
    }

    #[Route('/{id}/offers', name: 'create_offer', methods: ['POST'])]
    public function createOffer(
        Company $company,
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $offer = new InternshipOffer();
        $offer->setCompany($company);
        $offer->setTitle($data['title']);
        $offer->setDescription($data['description']);
        $offer->setType($data['type']);
        $offer->setStartDate(new \DateTime($data['startDate']));
        $offer->setEndDate(new \DateTime($data['endDate']));
        $offer->setDepartment($data['department']);
        
        $entityManager->persist($offer);
        $entityManager->flush();
        
        return $this->json($offer, 201);
    }
} 