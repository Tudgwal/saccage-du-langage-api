<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Quote;
use App\Entity\Politician;
use App\Entity\Party;
use App\Entity\User;

#[Route('/quote', name: 'app_quote')]
class QuoteController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $quotes = $entityManager->getRepository(Quote::call)->findAll();
        
        $data = [];
        foreach ($quotes as $quote){
            $date[] = [
                'id' => $quote->getId(),
                'quote' => $quote->getQuote(),
                'link' => $quote->getLink(),
                'private' => $quote->isPrivateLink(),
                'date' => $quote->getDate(),
                'politician' => $quote->getPolitician(),
                'party' => $quote->getParty(),
                'user' => $quote->getUser()
            ];
        }
        return $this->json($data);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getOne(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $quote = $entityManager->getRepository(Quote::class)->find($id);

        if (!$quote) {
            return $this->json('Quote not fond', 404);
        }

        return $this->json([
            'id' => $quote->getId(),
            'quote' => $quote->getQuote(),
            'link' => $quote->getLink(),
            'private' => $quote->isPrivateLink(),
            'date' => $quote->getDate(),
            'politician' => $quote->getPolitician(),
            'party' => $quote->getParty(),
            'user' => $quote->getUser()
        ]);
    }

    #[Route('/create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {

        if (!$request->request->get('quote') || empty($request->request->get('quote')) )
            return $this->json('A quote is needed', 404);
        
        if (!$request->request->get('link') || empty($request->request->get('link')))
            return $this->json('A link is needed', 404);

        if (!$request->request->get('private') || empty($request->request->get('private')))
            return $this->json('We need to know if the link is private or not', 404);

        if (!$request->request->get('date') || empty($request->request->get('date')))
            return $this->json('You need to provide the date when the quote was said', 404);

        if (!$request->request->get('politician') || empty($request->request->get('politician')))
            return $this->json('You need to give a politician', 404);

        if (!$request->request->get('party') || empty($request->request->get('party')))
            return $this->json('You need to give the party of the politician at the time of the quote', 404);

        $politician = $entityManager->getRepository(Politician::class)->fin($request->request->get('politician'));

        if (!$politician || $politician == NULL)
            return $this->json('Politician not found', 404);

        $party = $entityManager->getRepository(Party::class)->find($request->request->get('party'));

        if (!$party || $party == NULL)
            return $this->json('Party not found', 404);

        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        if (!$user || $user == NULL)
            return $this->json('User not found', 404);

        $quote = new Quote();
        $quote->setQuote($request->request->get('quote'));
        $quote->setLink($request->request->get('link'));
        $quote->setPrivateLink($request->request->get('private'));
        $quote->setDate($request->request->get('date'));
        $quote->setPolitician($politician);
        $quote->setParty($party);
        $quote->setUser($user);
        $quote->setTimestamp(new \DateTime());

        $entityManager->persist($quote);
        $entityManager->flush();

        return $this->json([
            'id' => $quote->getId(),
            'quote' => $quote->getQuote(),
            'link' => $quote->getLink(),
            'private' => $quote->isPrivateLink(),
            'date' => $quote->getDate(),
            'politician' => $quote->getPolitician(),
            'party' => $quote->getParty(),
            'user' => $quote->getUser()
        ]);
    }

    #[Route('/delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        $quote = $entityManager->getRepository(Quote::class)->find($request->request->get('quote'));

        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles()) || 
            $user->getId() == $quote->getUser()->getId()){
            $entityManager->remove($quote);
        } else {
            return $this->json('You can not do this', HTTP::UNAUTHORIZE);
        }

        $entityManager->flush();

        return $this->json('Quote deleted');
    }
}
