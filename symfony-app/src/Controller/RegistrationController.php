<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserType;
use App\Model\UserFormData;
use App\Service\UserCatalog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserCatalog $catalog
    ): Response {
        $userData = new UserFormData();

        $form = $this->createForm(UserType::class, $userData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $catalog->create($userData);

                $this->addFlash(
                    'success',
                    'Регистрация прошла успешно! Теперь вы можете войти.'
                );

                return $this->redirectToRoute('app_login');
            } catch (UniqueConstraintViolationException $e) {
                $form->get('email')->addError(
                    new FormError('Пользователь с таким email уже существует.')
                );
            }
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'form' => $form->createView(),
            ],
            new Response(
                '',
                $form->isSubmitted() ? 422 : 200
            )
        );
    }
}
