<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/user', name: 'app_user')]
class UserController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(EntityManagerInterface $entityManager) : JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getUsername(),
                'email' => $user->getEmail()
            ];
        }
        return $this->json($data);
    }

    #[Route('/me', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
        dump($user); exit;
        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getUsername()
        ]);
    }

    #[Route('/create', name: "user_register" , methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher) : JsonResponse
    {
        $user = new User();
        $user->setUsername($request->request->get('name'));
        $user->setPassword($userPasswordHasher->hashPassword($user, $request->request->get('password')));
        $user->setEmail($request->request->get('email'));
        $user->setRoles(array('ROLE_USER'));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getUsername()
        ]);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function showOne(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json('User not found', 404);
        }

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getUsername()
        ]);
    }

    #[Route('/edit', methods: ['PUT', 'PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(EntityManagerInterface $entityManager, Request $request) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());

        if ($request->request->has('name')) {
            $user->setUsername($request->request->get('name'));
        }

        if ($request->request->has('password')) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $request->request->get('password')));
        }

        if ($request->request->has('email')) {
            $user->setEmail($request->request->get('email'));
        }

        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getUsername(),
            'email' => $user->getEmail()
        ]);
    }

    #[Route('/delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, int $id) : JsonResponse
    {
        $id = $request->request->get('user');

        if (!$id || empty($id))
            return $this->json('You need to provide a user', 404);

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json('User not found', 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json('User deleted');
    }

    #[Route('/deleteme', methods: ['DELETE'])]
    public function deleteme(EntityManagerInterface $entityManager) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getId());
        
        if (!$user) {
            return $this->json('User not found', 404);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json('User deleted');
    }
    
    #[Route('/promote/{id}', methods: ['POST'])]
    public function promote(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json('User not found', 404);
        }

        $user->setRoles(array('ROLE_ADMIN', 'ROLE_USER'));
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/demote/{id}', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function demote(EntityManagerInterface $entityManager, int $id) : JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json('User not found', 404);
        }

        $user->setRoles(array('ROLE_USER'));
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getUsername(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ]);
    }
}
