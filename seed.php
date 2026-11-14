<?php

declare(strict_types=1);


require_once __DIR__ . '/database/seeders/DatabaseSeeder.php';

$pdo = require __DIR__ . '/config/database.php';

$seeder = new DatabaseSeeder();

$seeder->run($pdo);

echo "Databse seeded succesfully";