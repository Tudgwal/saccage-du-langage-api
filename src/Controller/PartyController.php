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

    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager) : JsonResponse
    {
        $partys = $entityManager->getRepository(Party::class)->findAll();

        $data = [];

        foreach ($partys as $party) {
            $data[] = [
                'id' => $party->getId(),
                'name' => $party->getName(),
                'logo' => $party->getLogo(),
                'user' => $party->getUser()->getId()
            ];
        }

        return $this->json($data);
    }

    #[Route('/create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        $party = new Party();
        $party->setName($request->request->get('name'));
        $party->setLogo($request->request->get('logo'));
        $party->setUser($user);

        $entityManager->persist($party);
        $user->addParty($party);
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
            return $this->json('Party not found', 404);
        }

        return $this->json([
            'id' => $party->getId(),
            'name' => $party->getName(),
            'logo' => $party->getLogo(),
            'user' => $party->getUser()->getId()
        ]);
    }

    #[Route('/edit', methods: ['POST'])]
    public function update(EntityManagerInterface $entityManager, Request $request, int $id) : JsonResponse
    {

        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        if (!in_array('ROLE_ADMIN', $user->getRoles()) || 
            $user->getId() != $party->getUser()->getId()){
            return $this->json('You can not edit this party', HHTP::UNAUTHORIZE);
        }

        $party = $entityManager->getRepository(Party::class)->find($request->request->get('party'));

        if (!$party) {
            return $this->json('Party not found', 404);
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

    #[Route('/delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        if (!$request->request->get('party') || empty($request->request->get('party')) )
            return $this->json('A party is needed', 404);

        $party = $entityManager->getRepository(Party::class)->find($request->request->get('party'));

        if (!$party) {
            return $this->json('Party not found', 404);
        }

        $name = $party->getName();

        $user = $party->getUser();
        $user->removeParty($party);

        $entityManager->remove($party);
        $entityManager->flush();

        return $this->json('Deleted successfully party ' . $name);
    }
}
