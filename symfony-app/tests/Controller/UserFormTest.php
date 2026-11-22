<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserFormTest extends WebTestCase
{
    public function testCreateFormOpens(): void
    {
        $client = static::createClient();

        $client->request('GET', '/users/new');

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorExists('input[name="user[name]"]');
        self::assertSelectorExists('input[name="user[email]"]');
        self::assertSelectorExists('input[name="user[plainPassword]"]');
    }

    public function testFormHasCsrfToken(): void
    {
        $client = static::createClient();

        $client->request('GET', '/users/new');

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorExists('input[name="user[_token]"]');
    }

    public function testInvalidUserData(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users/new');

        $form = $crawler->selectButton('Создать пользователя')->form([
            'user[name]' => '',
            'user[email]' => 'invalid-email',
            'user[plainPassword]' => '123',
        ]);

        $client->submit($form);

        self::assertResponseStatusCodeSame(200);

        self::assertSelectorExists(
            'input[name="user[name]"][aria-invalid="true"]'
        );

        self::assertSelectorExists(
            'input[name="user[email]"][aria-invalid="true"]'
        );

        self::assertSelectorExists(
            'input[name="user[plainPassword]"][aria-invalid="true"]'
        );
    }

    public function testInvalidCsrfToken(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/users/new');

        $form = $crawler->selectButton('Создать пользователя')->form([
            'user[name]' => 'Test User',
            'user[email]' => 'csrf-test@example.com',
            'user[plainPassword]' => 'TestPassword123',
            'user[_token]' => 'invalid-token',
        ]);

        $client->submit($form);

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('form', 'CSRF');
    }

    public function testSearchEscapesXss(): void
    {
        $client = static::createClient();

        $payload = '<script>alert("xss")</script>';

        $client->request(
            'GET',
            '/users?search=' . urlencode($payload)
        );

        self::assertResponseStatusCodeSame(200);

        $content = (string) $client->getResponse()->getContent();

        self::assertStringNotContainsString($payload, $content);

        self::assertStringContainsString(
            '&lt;script&gt;',
            $content
        );
    }

    public function testInvalidSortDoesNotCauseSqlError(): void
    {
        $client = static::createClient();

        $client->request(
            'GET',
            '/users?sort=' . urlencode('name; DROP TABLE users')
        );

        self::assertResponseStatusCodeSame(200);
    }
}
