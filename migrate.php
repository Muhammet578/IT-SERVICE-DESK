<?php

declare(strict_types=1);

require_once __DIR__ . '/database/MigrationRunner.php';

$pdo = require __DIR__ . '/config/database.php';

$runner = new MigrationRunner($pdo);

$runner->runMigrations();