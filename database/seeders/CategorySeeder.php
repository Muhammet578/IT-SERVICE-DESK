<?php

declare(strict_types=1);

class CategorySeeder{
    public function run(PDO $pdo): void {

        $sql = "
            INSERT INTO categories(name, description)
            VALUES (:name, :description)
            ON DUPLICATE KEY UPDATE
                description = VALUES(description)
            ";

        $stmt = $pdo->prepare($sql);

        $categories = [
            [
                'name'          => 'Оборудование',
                'description'   => 'Проблема с компом',
            ],
            [
                'name'          => 'Программное обеспечение',
                'description'   => 'Ошибка, установки',
            ],
            [
                'name'          => 'Сеть и интернет',
                'description'   => 'Проблемы с подключением',
            ],
             [
                'name'          => 'Учетные записи',
                'description'   => 'Создание учетных записей и управление доступом',
            ],
            [
                'name'          => 'Электронная почта',
                'description'   => 'Проблемы с корпоративной электронной почтой',
            ],
            [
                'name'          => 'Другое',
                'description'   => 'Прочие технические вопросы',
            ],
        ];

        foreach($categories as $category){
            $stmt->execute($category);
        }
    }
}