<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserEditType;
use App\Form\UserType;
use App\Model\UserEditData;
use App\Model\UserFormData;
use App\Repository\UserRepository;
use App\Service\UserCatalog;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    // Список пользователей с пагинацией
    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function index(
        Request $request,
        UserRepository $userRepository
    ): Response {
        $search = trim(
            (string) $request->query->get('search', '')
        );

        $sort = (string) $request->query->get('sort', 'id');

        $page = max(
            1,
            $request->query->getInt('page', 1)
        );

        $limit = 15;

        $result = $userRepository->paginate(
            $page,
            $limit,
            $search,
            $sort
        );

        $totalPages = max(
            1,
            (int) ceil($result['total'] / $limit)
        );

        if ($page > $totalPages) {
            return $this->redirectToRoute('app_users', [
                'page' => $totalPages,
                'search' => $search,
                'sort' => $sort,
            ]);
        }

        return $this->render('user/index.html.twig', [
            'users' => $result['users'],
            'search' => $search,
            'sort' => $sort,
            'total' => $result['total'],
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    // Создание пользователя
    #[Route('/users/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        UserCatalog $catalog
    ): Response {
        $userData = new UserFormData();

        $form = $this->createForm(
            UserType::class,
            $userData
        );

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
                    new FormError(
                        'Пользователь с таким email уже существует'
                    )
                );

                return $this->render(
                    'user/new.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                    new Response('', 422)
                );
            }
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Редактирование пользователя
    #[Route(
        '/users/{id}/edit',
        name: 'app_users_edit',
        requirements: ['id' => '[1-9][0-9]*'],
        methods: ['GET', 'POST']
    )]
    public function edit(
        int $id,
        Request $request,
        UserCatalog $catalog
    ): Response {
        $user = $catalog->findById($id);

        if ($user === null) {
            throw $this->createNotFoundException(
                'Пользователь не найден'
            );
        }

        $userData = new UserEditData();
        $userData->name = $user['name'];
        $userData->email = $user['email'];

        $form = $this->createForm(
            UserEditType::class,
            $userData
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $catalog->update($id, $userData);

                $this->addFlash(
                    'success',
                    'Данные пользователя обновлены'
                );

                return $this->redirectToRoute('app_users');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(
                    new FormError(
                        'Пользователь с таким email существует'
                    )
                );
            }
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ],
            new Response(
                '',
                $form->isSubmitted() ? 422 : 200
            )
        );
    }
}
