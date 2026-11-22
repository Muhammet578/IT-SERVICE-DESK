<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class UserEditData
{
    #[Assert\NotBlank(message: 'Введите имя пользователя')]
    #[Assert\Length(min: 2, max: 150)]
    public string $name = '';

    #[Assert\NotBlank(message: 'Введите email')]
    #[Assert\Email(message: 'Некорректный email')]
    #[Assert\Length(max: 255)]
    public string $email = '';
}
