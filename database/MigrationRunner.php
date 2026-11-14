<?php

declare(strict_types=1);

final class MigrationRunner{

    private PDO $pdo;
    
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function createMigrationsTable(): void
     {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations(
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(250) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
    
        $this->pdo->exec($sql);
    }

    
    public function runMigrations(): void
{
    $this->createMigrationsTable();

    $files = glob(__DIR__ . '/migrations/*.php');

    sort($files);

    foreach ($files as $file) {
        $migration = basename($file);

        $statement = $this->pdo->prepare(
            'SELECT COUNT(*) FROM migrations WHERE migration = :migration'
        );

        $statement->execute(['migration' => $migration]);

        if ((int) $statement->fetchColumn() > 0) {
            continue;
        }

        $sql = require $file;

        $this->pdo->exec($sql);

        $statement = $this->pdo->prepare(
            'INSERT INTO migrations (migration) VALUES (:migration)'
        );

        $statement->execute(['migration' => $migration]);

        echo "Migrated: {$migration}" . PHP_EOL;
    }
}
}