<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Party;
use Symfony\Component\HttpFoundation\Request;

#[Route('/party', name: 'app_party')]
class PartyController extends AbstractController
{

    #[Route('/', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager) : JsonResponse
    {
        $partys = $entityManager->getRepository(Party::class)->findAll();

        $data = [];

        foreach ($partys as $party) {
            $data[] = [
                'id' => $party->getId(),
                'name' => $party->getName(),
                'logo' => $party->getLogo()
            ];
        }

        return $this->json($data);
    }

    #[Route('/', methods: ['POST'])]
    public function create(EntityManager $entityManager, Request $request) : JsonResponse
    {
        $party = new Party();
        $party->setName($request->request->get('name'));
        $party->setLogo($request->request->get('logo'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($party);
        $entityManager->flush();

        return $this->json([
            'id' => $party->getId(),
            'name' => $party->getName(),
            'logo' => $party->getLogo()
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $party = $entityManager->getRepository(Party::class)->find($id);

        if (!$party) {
            throw $this->json('Party not found', 404);
        }

        return $this->json([
            'id' => $party->getId(),
            'name' => $party->getName(),
            'logo' => $party->getLogo()
        ]);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id) : JsonResponse
    {
        $party = $entityManager->getRepository(Party::class)->find($id);

        if (!$party) {
            throw $this->json('Party not found', 404);
        }

        if ($request->request->has('name')) {
            $party->setName($request->request->get('name'));
        }

        if ($request->request->has('logo')) {
            $party->setLogo($request->request->get('logo'));
        }

        $entityManager->flush();

        return $this->json([
            'id' => $party->getId(),
            'name' => $party->getName(),
            'logo' => $party->getLogo()
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $party = $entityManager->getRepository(Party::class)->find($id);

        if (!$party) {
            throw $this->json('Party not found', 404);
        }

        $name = $party->getName();

        $entityManager->remove($party);
        $entityManager->flush();

        return $this->json('Deleted successfully party ' . $name);
    }
}
