<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\SecurityHttp\Attribute\IsGranted;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

#[Route('/user', name: 'app_user')]
class UserController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index() : JsonResponse
    {
        return $this->json("We don't display all users", 200);
    }

    #[Route('/me', methods: ['GET'])]
    #[IsGranted("ROLE_USER")]
    public function show(Request $request) : JsonResponse
    {

        var_dump( $request->headers->all());
        return $this->json($this->getUser());
    }

    #[Route('', name: "user_register" , methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher) : JsonResponse
    {
        $user = new User();
        $user->setUsername($request->request->get('name'));
        $user->setPassword($userPasswordHasher->hashPassword($user, $request->request->get('password')));
        $user->setEmail($request->request->get('email'));
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getUsername()
        ]);
    }
}
