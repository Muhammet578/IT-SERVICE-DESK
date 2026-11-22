<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\UserFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

final class UserType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->add('name', TextType::class, [
            'label' => 'Имя пользователя',
            'attr'  => [
                'placeholder' => 'Введите имя',
                'class' => 'form-control',
            ],
        ]);
        $builder->add('plainPassword', PasswordType::class, [
            'label' => 'Пароль пользователя',
            'attr'  => [
                'placeholder' => 'Введите пароль',
                'class'       => 'form-control',
                'autocomplete' => 'new-password',
            ]
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'Email пользователя',
            'attr'  => [
                'placeholder' => 'Введите email',
                'class'       => 'form-control',
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserFormData::class,
        ]);
    }
}
