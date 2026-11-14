<?php

declare(strict_types=1);

return "
    CREATE TABLE comments (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT UNSIGNED NOT NULL,
        user_id INT UNSIGNED NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

        CONSTRAINT fk_comments_ticket
            FOREIGN KEY (ticket_id)
            REFERENCES tickets(id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

        CONSTRAINT fk_comments_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,

        INDEX idx_comments_ticket (ticket_id),
        INDEX idx_comments_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";