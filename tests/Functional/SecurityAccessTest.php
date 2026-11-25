<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Role;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Запускать только с отдельной тестовой БД * _test.
 * В этой БД должны быть активные пользователи с ролями:
 * admin, manager, technician, user (через fixtures).
 */
final class SecurityAccessTest extends WebTestCase
{
    private function loginAs(KernelBrowser $client, string $roleName): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->assertTestDatabase($em);

        $role = $em->getRepository(Role::class)->findOneBy(['name' => $roleName]);
        self::assertNotNull($role, sprintf('В тестовой БД нет роли "%s".', $roleName));

        $user = $em->getRepository(User::class)->findOneBy([
            'role' => $role,
            'isActive' => true,
        ]);
        self::assertNotNull($user, sprintf('В тестовой БД нет активного пользователя с ролью "%s".', $roleName));

        $client->loginUser($user);
    }

    private function assertTestDatabase(EntityManagerInterface $em): void
    {
        $database = (string) ($em->getConnection()->getParams()['dbname'] ?? '');
        self::assertStringContainsString(
            '_test',
            strtolower($database),
            'ОПАСНО: тесты разрешены только для отдельной БД, название которой содержит _test.'
        );
    }

    public function testLoginPageAvailableToGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        self::assertResponseStatusCodeSame(200);
    }

    public function testGuestRedirectedFromDashboard(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dashboard');
        self::assertResponseStatusCodeSame(302);
    }

    public function testGuestRedirectedFromProfile(): void
    {
        $client = static::createClient();
        $client->request('GET', '/profile');
        self::assertResponseStatusCodeSame(302);
    }

    public function testUserCanOpenOwnProfile(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'user');
        $client->request('GET', '/profile');
        self::assertResponseStatusCodeSame(200);
    }

    public function testUserCannotOpenUserManagement(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'user');
        $client->request('GET', '/users');
        self::assertResponseStatusCodeSame(403);
    }

    public function testUserCannotCreateAnotherUser(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'user');
        $client->request('GET', '/users/new');
        self::assertResponseStatusCodeSame(403);
    }

    public function testTechnicianCanOpenTickets(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'technician');
        $client->request('GET', '/tickets');
        self::assertResponseStatusCodeSame(200);
    }

    public function testManagerCanOpenUserCreation(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'manager');
        $client->request('GET', '/users/new');
        self::assertResponseStatusCodeSame(200);
    }

    public function testManagerCannotOpenAdminUserList(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'manager');
        $client->request('GET', '/users');
        self::assertResponseStatusCodeSame(403);
    }

    public function testAdminCanOpenUserList(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin');
        $client->request('GET', '/users');
        self::assertResponseStatusCodeSame(200);
    }

    public function testUserCannotViewForeignTicket(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->assertTestDatabase($em);

        $role = $em->getRepository(Role::class)->findOneBy(['name' => 'user']);
        self::assertNotNull($role);
        $user = $em->getRepository(User::class)->findOneBy(['role' => $role, 'isActive' => true]);
        self::assertNotNull($user);

        // Ищем чужую заявку в отдельной тестовой базе.
        $foreignTicket = $em->getRepository(Ticket::class)
            ->createQueryBuilder('t')
            ->where('t.createdBy != :viewer')
            ->setParameter('viewer', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNotNull($foreignTicket, 'Добавьте чужую заявку в тестовые fixtures.');
        $client->loginUser($user);

        $url = static::getContainer()->get('router')->generate('find_tickets', [
            'id' => $foreignTicket->getId(),
        ]);
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    public function testUserCannotChangeStatusOfForeignTicket(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->assertTestDatabase($em);

        $role = $em->getRepository(Role::class)->findOneBy(['name' => 'user']);
        self::assertNotNull($role);
        $user = $em->getRepository(User::class)->findOneBy(['role' => $role, 'isActive' => true]);
        self::assertNotNull($user);

        $foreignTicket = $em->getRepository(Ticket::class)
            ->createQueryBuilder('t')
            ->where('t.createdBy != :viewer')
            ->setParameter('viewer', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        self::assertNotNull($foreignTicket, 'Добавьте чужую заявку в тестовые fixtures.');
        $client->loginUser($user);

        $url = static::getContainer()->get('router')->generate('app_tickets_status', [
            'id' => $foreignTicket->getId(),
        ]);
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(403);
    }

    public function testInvalidCsrfCannotDeleteTicket(): void
    {
        $client = static::createClient();
        $this->loginAs($client, 'admin');
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $this->assertTestDatabase($em);

        $ticket = $em->getRepository(Ticket::class)->findOneBy([]);
        self::assertNotNull($ticket, 'В тестовой БД нет заявки для проверки CSRF.');
        $ticketId = $ticket->getId();

        $client->request(
            'POST',
            static::getContainer()->get('router')->generate('app_tickets_delete', ['id' => $ticketId]),
            ['_token' => 'invalid-csrf-token']
        );

        // Проверяем конечный результат независимо от 302/403 обработки ошибки.
        $em->clear();
        self::assertNotNull(
            $em->getRepository(Ticket::class)->find($ticketId),
            'Удаление прошло с поддельным CSRF-токеном.'
        );
    }
}
