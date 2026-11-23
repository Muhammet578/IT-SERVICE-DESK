<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Priority;
use App\Entity\Role;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ==========================================
        // 1. РОЛИ
        // ==========================================

        $roles = [
            ['admin', 'Администратор системы'],
            ['manager', 'Менеджер IT-отдела'],
            ['user', 'Обычный пользователь'],
        ];

        $rolesByName = [];

        foreach ($roles as [$name, $description]) {
            $role = new Role($name, $description);

            $manager->persist($role);
            $rolesByName[$name] = $role;
        }

        // ==========================================
        // 2. ПРИОРИТЕТЫ
        // ==========================================

        $priorities = [
            ['Низкий', 1],
            ['Средний', 2],
            ['Высокий', 3],
            ['Критический', 4],
        ];

        $priorityEntities = [];

        foreach ($priorities as [$name, $level]) {
            $priority = new Priority($name, $level);

            $manager->persist($priority);
            $priorityEntities[] = $priority;
        }

        // ==========================================
        // 3. СТАТУСЫ
        // ==========================================

        $statuses = [
            ['Новая', 1, false],
            ['В работе', 2, false],
            ['Решена', 3, true],
            ['Закрыта', 4, true],
        ];

        $statusEntities = [];

        foreach ($statuses as [$name, $sortOrder, $isFinal]) {
            $status = new Status(
                $name,
                $sortOrder,
                $isFinal
            );

            $manager->persist($status);
            $statusEntities[] = $status;
        }

        // ==========================================
        // 4. КАТЕГОРИИ — 30
        // ==========================================

        $categories = [
            'Компьютеры',
            'Ноутбуки',
            'Мониторы',
            'Принтеры',
            'Сканеры',
            'Клавиатуры',
            'Мыши',
            'Периферийные устройства',
            'Операционные системы',
            'Офисные программы',
            'Антивирусное ПО',
            'Установка программ',
            'Обновление программ',
            'Ошибки программ',
            'Локальная сеть',
            'Интернет',
            'Wi-Fi',
            'VPN',
            'Сетевое оборудование',
            'Серверы',
            'Базы данных',
            'Электронная почта',
            'Корпоративные аккаунты',
            'Восстановление пароля',
            'Права доступа',
            'Информационная безопасность',
            'Резервное копирование',
            'Восстановление данных',
            'Телефония',
            'Другое',
        ];

        $categoryEntities = [];

        foreach ($categories as $name) {
            $category = new Category($name);

            $manager->persist($category);
            $categoryEntities[] = $category;
        }

        // ==========================================
        // 5. ПОЛЬЗОВАТЕЛИ — 20
        // ==========================================

        $userEntities = [];

        // Только для локальной разработки!
        $passwordHash = password_hash(
            'DemoPassword2026!',
            PASSWORD_DEFAULT
        );

        for ($i = 1; $i <= 20; $i++) {
            $user = new User();

            $user->setName("Сотрудник $i");

            $user->setEmail(
                sprintf('employee%02d@example.test', $i)
            );

            $user->setPasswordHash($passwordHash);

            // Каждый шестой пользователь неактивен
            $user->setIsActive($i % 6 !== 0);

            if ($i === 1) {
                $roleName = 'admin';
            } elseif ($i <= 5) {
                $roleName = 'manager';
            } else {
                $roleName = 'user';
            }

            $user->setRole($rolesByName[$roleName]);

            $manager->persist($user);
            $userEntities[] = $user;
        }

        // Пользователи с ролью manager
        $managerUsers = array_slice($userEntities, 1, 4);

        // ==========================================
        // 6. ЗАЯВКИ — 50
        // ==========================================

        $ticketEntities = [];

        $ticketTitles = [
            'Не включается компьютер',
            'Не работает интернет',
            'Ошибка при запуске программы',
            'Не печатает принтер',
            'Не удаётся войти в систему',
            'Не работает корпоративная почта',
            'Требуется установка программы',
            'Не подключается VPN',
            'Не работает монитор',
            'Необходимо восстановить пароль',
        ];

        for ($i = 1; $i <= 50; $i++) {
            $ticket = new Ticket();

            $title = $ticketTitles[($i - 1) % count($ticketTitles)];

            $ticket->setTitle(
                sprintf('%s — заявка №%d', $title, $i)
            );

            $ticket->setDescription(
                sprintf(
                    'Тестовая заявка №%d. Требуется помощь IT-отдела.',
                    $i
                )
            );

            // Автор заявки
            $ticket->setCreatedBy(
                $userEntities[($i - 1) % count($userEntities)]
            );

            // Каждая пятая заявка без исполнителя
            if ($i % 5 !== 0) {
                $ticket->setAssignedTo(
                    $managerUsers[($i - 1) % count($managerUsers)]
                );
            }

            // Категория
            $ticket->setCategory(
                $categoryEntities[($i - 1) % count($categoryEntities)]
            );

            // Приоритет
            $ticket->setPriority(
                $priorityEntities[($i - 1) % count($priorityEntities)]
            );

            // Статус
            $ticket->setStatus(
                $statusEntities[($i - 1) % count($statusEntities)]
            );

            $manager->persist($ticket);
            $ticketEntities[] = $ticket;
        }

        // ==========================================
        // 7. КОММЕНТАРИИ — 100
        // ==========================================

        $commentTexts = [
            'Заявка принята в обработку.',
            'Проводится диагностика проблемы.',
            'Требуется дополнительная информация.',
            'Специалист занимается решением.',
            'Проблема успешно устранена.',
        ];

        for ($i = 1; $i <= 100; $i++) {
            $ticket = $ticketEntities[($i - 1) % count($ticketEntities)];

            $user = $userEntities[($i - 1) % count($userEntities)];

            $content = $commentTexts[($i - 1) % count($commentTexts)];

            $comment = new Comment(
                $ticket,
                $user,
                $content
            );

            $manager->persist($comment);
        }

        // ==========================================
        // 8. СОХРАНЕНИЕ В БАЗУ
        // ==========================================

        $manager->flush();
    }
}
