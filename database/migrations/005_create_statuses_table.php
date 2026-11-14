<?php

declare(strict_types=1);

return "
    CREATE TABLE statuses (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        is_final BOOLEAN NOT NULL DEFAULT FALSE,
        sort_order TINYINT UNSIGNED NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";