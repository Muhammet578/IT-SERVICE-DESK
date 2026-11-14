<?php

declare(strict_types=1);

return "
    CREATE TABLE tickets (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,

        created_by INT UNSIGNED NOT NULL,
        assigned_to INT UNSIGNED NULL,

        category_id INT UNSIGNED NOT NULL,
        priority_id INT UNSIGNED NOT NULL,
        status_id INT UNSIGNED NOT NULL,

        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ON UPDATE CURRENT_TIMESTAMP,

        CONSTRAINT fk_tickets_creator
            FOREIGN KEY (created_by)
            REFERENCES users(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,

        CONSTRAINT fk_tickets_assignee
            FOREIGN KEY (assigned_to)
            REFERENCES users(id)
            ON UPDATE CASCADE
            ON DELETE SET NULL,

        CONSTRAINT fk_tickets_category
            FOREIGN KEY (category_id)
            REFERENCES categories(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,

        CONSTRAINT fk_tickets_priority
            FOREIGN KEY (priority_id)
            REFERENCES priorities(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,

        CONSTRAINT fk_tickets_status
            FOREIGN KEY (status_id)
            REFERENCES statuses(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT,

        INDEX idx_tickets_created_by (created_by),
        INDEX idx_tickets_assigned_to (assigned_to),
        INDEX idx_tickets_status (status_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";