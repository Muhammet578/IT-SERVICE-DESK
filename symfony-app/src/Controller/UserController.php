<?php

namespace App\Controller;

use App\Service\UserCatalog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserType;
use App\Model\UserFormData;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
use App\Form\UserEditType;
use App\Model\UserEditData;

final class UserController extends AbstractController
{
    #[Route('/users/{id}/edit', name: 'app_users_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, UserCatalog $catalog): Response
    {
        $user = $catalog->findById($id);
        if ($user === null) {
            throw $this->createNotFoundException('Пользователь не найден');
        }
        $userData = new UserEditData();
        $userData->name = $user['name'];
        $userData->email = $user['email'];

        $form = $this->createForm(UserEditType::class, $userData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
         try {
            $catalog->update($id, $userData);

            $this->addFlash('success', 'Данные пользователя обновлены');

            return $this->redirectToRoute('app_users');
            }catch(UniqueConstraintViolationException $e){
                $form->get('email')->addError(
                    new FormError('Пользователь с таким email существует')
                );
            }   
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ], new Response('', $form->isSubmitted() ? 422 : 200));
    }

    #[Route('/users/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserCatalog $catalog): Response
    {
        $userData = new UserFormData();

        $form = $this->createForm(UserType::class, $userData);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $catalog->create($userData);

                $this->addFlash(
                    'success',
                    'Пользователь успешно создан!'
                );

                return $this->redirectToRoute('app_users');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(
                    new FormError('Пользователь с таким email уже существует')
                );

                return $this->render('user/new.html.twig', [
                    'form' => $form->createView(),
                ], new Response('', 422));
            }
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function index(Request $request, UserCatalog $catalog): Response
    {
        $search = trim((string) $request->query->get('search', ''));
        $sort = (string) $request->query->get('sort', 'id');

        return $this->render('user/index.html.twig', [
            'users' => $catalog->findAll($search, $sort),
            'search' => $search,
            'sort' => $sort,
        ]);
    }
}
