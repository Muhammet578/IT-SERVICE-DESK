<?php

namespace App\Controller;

use App\Service\UserCatalog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function index(UserCatalog $catalog): Response
    {
        return $this->render('user/index.html.twig', ['users' => $catalog->findAll()]);
    }

    #[Route('/users/{id}', name: 'find_user', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    public function findUser(int $id, UserCatalog $catalog): Response
    {
        $user = $catalog->findById($id);
        if ($user === null) {
            throw $this->createNotFoundException('Пользователь не найден');
        }
        return $this->render('user/show.html.twig', ['user' => $user]);
    }
}
