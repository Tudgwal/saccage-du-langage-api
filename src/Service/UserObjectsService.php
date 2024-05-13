<?php declare(strict_types=1);

// src/Service/UserObjectsService.php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserObjectsService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function purgeQuotesByUser(User $user) : void
    {
        $quotes = $user->getQuotes();
        foreach ($quotes as $quote) {
            $this->entityManager->remove($quote);
        }
        $quotes->clear();
        $this->entityManager->flush();
    }

    public function purgePartiesByUser(User $user) : void
    {
        $parties = $user->getParties();
        foreach ($parties as $party) {
            $this->entityManager->remove($party);
        }
        $parties->clear();
        $this->entityManager->flush();
    }

    public function purgePoliticiansByUser(User $user) : void
    {
        $politicians = $user->getPoliticians();
        foreach ($politicians as $politician) {
            $this->entityManager->remove($politician);
        }
        $politicians->clear();
        $this->entityManager->flush();
    }

    public function purgeVotesByUser(User $user) : void
    {
        $votes = $user->getVotes();
        foreach ($votes as $vote) {
            $this->entityManager->remove($vote);
        }
        $votes->clear();
        $this->entityManager->flush();
    }

    public function reownUserObjects(User $user, User $admin) : void
    {
        $quotes = $user->getQuotes();
        foreach ($quotes as $quote) {
            $quote->setUser($admin);
            $user->removeQuote($quote);
            $admin->addQuote($quote);
        }

        $parties = $user->getParties();
        foreach ($parties as $party) {
            $party->setUser($admin);
            $user->removeParty($party);
            $admin->addParty($party);
        }

        $politicians = $user->getPoliticians();
        foreach ($politicians as $politician) {
            $politician->setUser($admin);
            $user->removePolitician($politician);
            $admin->addPolitician($politician);
        }

        $votes = $user->getVotes();
        foreach ($votes as $vote) {
            $vote->setUser($admin);
            $user->removeVote($vote);
        }

        $this->entityManager->flush();
    }
}