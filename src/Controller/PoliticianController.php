<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Politician;
use Symfony\Component\HttpFoundation\Request;

#[Route('/politician', name: 'app_politician')]
class PoliticianController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager) : JsonResponse
    {
        $politicians = $entityManager->getRepository(Politician::class)->findAll();

        $data = [];

        foreach ($politicians as $politician) {
            $data[] = [
                'id' => $politician->getId(),
                'name' => $politician->getName(),
                'picture' => $politician->getPicture()
            ];
        }

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        $politician = new Politician();
        $politician->setName($request->request->get('name'));
        $politician->setPicture($request->request->get('picture'));

        $entityManager->persist($politician);
        $entityManager->flush();

        return $this->json([
            'id' => $politician->getId(),
            'name' => $politician->getName(),
            'picture' => $politician->getPicture()
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $politician = $entityManager->getRepository(Politician::class)->find($id);

        if (!$politician) {
            return $this->json('Politician not found', 404);
        }

        return $this->json([
            'id' => $politician->getId(),
            'name' => $politician->getName(),
            'picture' => $politician->getPicture()
        ]);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id) : JsonResponse
    {
        $politician = $entityManager->getRepository(Politician::class)->find($id);

        if (!$politician) {
            return $this->json('Politician not found', 404);
        }

        if ($request->request->has('name')) {
            $politician->setName($request->request->get('name'));
        }

        if ($request->request->has('picture')) {
            $politician->setPicture($request->request->get('picture'));
        }

        $entityManager->flush();

        return $this->json([
            'id' => $politician->getId(),
            'name' => $politician->getName(),
            'picture' => $politician->getPicture()
        ]);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $politician = $entityManager->getRepository(Politician::class)->find($id);

        if (!$politician) {
            return $this->json('Politician not found', 404);
        }

        $name = $politician->getName();

        $entityManager->remove($politician);
        $entityManager->flush();

        return $this->json('Deleted successfully politician ' . $name);
    }
}
