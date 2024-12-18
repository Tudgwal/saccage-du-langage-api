<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vote;

#[Route('/vote', name: 'app_vote')]
class VoteController extends AbstractController
{
    #[Route('/create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {

        if (!$this->getUser()) {
            return $this->json('You need to be logged in to vote', 401);
        }

        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        if (!$user) {
            return $this->json('User not found', 404);
        }

        $quote = $entityManager->getRepository(Quote::class)->find($request->request->get('quote'));

        if (!$quote) {
            return $this->json('Quote not found', 404);
        }

        if ($request->request->get('upvote') === 'true') {
            $vote = true;
        } elseif ($request->request->get('upvote') === 'false') {
            $vote = false;
        } else {
            return $this->json('Invalid vote', 400);
        }

        $vote = new Vote();
        $vote->setQuote($quote);
        $vote->setUser($user);
        $vote->setUpvote($vote);
        $vote->setDate(new \DateTime());

        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->json([
            'id' => $vote->getId(),
            'vote' => $vote->getVote(),
            'quote' => $vote->getQuote(),
            'user' => $vote->getUser()
        ]);
    }
}
