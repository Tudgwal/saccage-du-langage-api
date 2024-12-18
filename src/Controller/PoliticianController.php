<?php declare(strict_types=1);

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
                'picture' => $politician->getPicture(),
                'user' => $politician->getUser()->getId()
            ];
        }

        return $this->json($data);
    }

    #[Route('/create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        $politician = new Politician();
        $politician->setName($request->request->get('name'));
        $politician->setPicture($request->request->get('picture'));
        $politician->setUser($user);


        $entityManager->persist($politician);
        $user->addPolitician($politician);
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

    #[Route('/edit', methods: ['PUT', 'PATCH'])]
    public function update(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        if (!in_array('ROLE_ADMIN', $user->getRoles()) || 
            $user->getId() != $politician->getUser()->getId()){
            return $this->json('You can not edit this politician', HHTP::UNAUTHORIZE);
        }

        $politician = $entityManager->getRepository(Politician::class)->find($request->request->get('politician'));

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

    #[Route('/delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager) : JsonResponse
    {
        if (!$request->request->get('politician') || empty($request->request->get('politician')) )
            return $this->json('A politician is needed', 404);


        $politician = $entityManager->getRepository(Politician::class)->find($request->request->get('politician'));

        if (!$politician) {
            return $this->json('Politician not found', 404);
        }

        $name = $politician->getName();

        $user = $politician->getUser();
        $user->removePolitician($politician);

        $entityManager->remove($politician);
        $entityManager->flush();

        return $this->json('Deleted successfully politician ' . $name);
    }
}
