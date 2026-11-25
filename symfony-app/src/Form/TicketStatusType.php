<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TicketStatusType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder->add('status', EntityType::class, [
            'class' => Status::class,
            'choice_label' => 'name',
            'label' => 'Статус заявки',
            'placeholder' => 'Выберите статус',
            'required' => true,
            'attr' => [
                'class' => 'form-select',
            ],
        ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'csrf_protection' => true,
            'csrf_token_id' => 'ticket_status',
        ]);
    }
}
