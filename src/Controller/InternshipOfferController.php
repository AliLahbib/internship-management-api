<?php

namespace App\Controller;

use App\Entity\InternshipOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/offers')]
class InternshipOfferController extends AbstractController
{
    #[Route('', name: 'list_offers', methods: ['GET'])]
    public function list(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $type = $request->query->get('type');
        $department = $request->query->get('department');
        
        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('o')
            ->from(InternshipOffer::class, 'o')
            ->orderBy('o.createdAt', 'DESC');
            
        if ($type) {
            $queryBuilder->andWhere('o.type = :type')
                ->setParameter('type', $type);
        }
        
        if ($department) {
            $queryBuilder->andWhere('o.department = :department')
                ->setParameter('department', $department);
        }
        
        $offers = $queryBuilder->getQuery()->getResult();
        
        return $this->json($offers);
    }

    #[Route('/{id}', name: 'get_offer', methods: ['GET'])]
    public function show(InternshipOffer $offer): JsonResponse
    {
        return $this->json($offer);
    }
} 