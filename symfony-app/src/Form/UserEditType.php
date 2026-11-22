<?php

declare(strict_types=1);

namespace App\Form;

use App\Model\UserEditData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

final class UserEditType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->add('name', TextType::class, [
            'label' => 'Имя пользователя',
            'attr' => [
                'placeholder' => 'Введите имя',
                'class' => 'form-control',
            ]
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'Email пользователя',
            'attr' => [
                'placeholder' => 'Введите email',
                'class' => 'form-control',
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEditData::class,
        ]);
    }
}
