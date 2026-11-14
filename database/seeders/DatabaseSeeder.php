<?php

declare(strict_types=1);

require_once __DIR__ . '/RoleSeeder.php';
require_once __DIR__ . '/UserSeeder.php';
require_once __DIR__ . '/CategorySeeder.php';
require_once __DIR__ . '/PrioritySeeder.php';
require_once __DIR__ . '/StatusSeeder.php';
require_once __DIR__ . '/TicketSeeder.php';
require_once __DIR__ . '/CommentSeeder.php';



class DatabaseSeeder{
    public function run(PDO $pdo): void {
        $seeders = [
            new RoleSeeder(),
            new UserSeeder(),
            new CategorySeeder(),
            new PrioritySeeder(),
            new StatusSeeder(),
            new TicketSeeder(),
            new CommentSeeder(),
            ];

        foreach($seeders as $seeder){
            $seeder->run($pdo);
        }
    }
}