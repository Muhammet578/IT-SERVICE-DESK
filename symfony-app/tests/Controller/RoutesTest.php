<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RoutesTest extends WebTestCase
{
    public function testPagesReturn200(): void
    {
        $client = static::createClient();

        $routes = [
            '/',
            '/dashboard',
            '/about',
            '/users',
            '/users/new',
            '/tickets',
            '/system-info',
        ];

        foreach ($routes as $route) {
            $client->request('GET', $route);

            self::assertResponseStatusCodeSame(
                200,
                "Маршрут {$route} должен возвращать HTTP 200"
            );
        }
    }

    public function testHealthReturnsJson(): void
    {
        $client = static::createClient();

        $client->request('GET', '/health');

        self::assertResponseStatusCodeSame(200);

        $data = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame('ok', $data['status']);
    }

    public function testRequestInfo(): void
    {
        $client = static::createClient();

        $client->request('GET', '/request-info?name=Alex&page=2');

        self::assertResponseStatusCodeSame(200);

        $data = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertSame('GET', $data['method']);
        self::assertSame('/request-info', $data['path']);
        self::assertSame('Alex', $data['query']['name']);
        self::assertSame('2', $data['query']['page']);
        self::assertArrayHasKey('userAgent', $data);
    }

    public function testUnknownPageReturns404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/unknown-url');

        self::assertResponseStatusCodeSame(404);
    }

    public function testUnknownUserReturns404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/users/999999/edit');

        self::assertResponseStatusCodeSame(404);
    }

    public function testInvalidUserIdReturns404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/users/abc/edit');

        self::assertResponseStatusCodeSame(404);
    }
}
