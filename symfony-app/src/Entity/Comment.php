<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'comments')]
#[ORM\Index(
    name: 'idx_comments_ticket',
    columns: ['ticket_id']
)]
#[ORM\Index(
    name: 'idx_comments_user',
    columns: ['user_id']
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        type: 'integer',
        options: ['unsigned' => true]
    )]
    private ?int $id = null;

    // Заявка, к которой относится комментарий
    #[ORM\ManyToOne(targetEntity: Ticket::class)]
    #[ORM\JoinColumn(
        name: 'ticket_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private Ticket $ticket;

    // Автор комментария
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'RESTRICT'
    )]
    private User $user;

    // Текст комментария
    #[ORM\Column(type: 'text')]
    private string $content;

    // Дата создания
    #[ORM\Column(
        name: 'created_at',
        type: 'datetime_immutable'
    )]
    private \DateTimeImmutable $createdAt;

    // Конструктор
    public function __construct(
        Ticket $ticket,
        User $user,
        string $content
    ) {
        $this->ticket = $ticket;
        $this->user = $user;
        $this->content = $content;
        $this->createdAt = new \DateTimeImmutable();
    }

    // ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Заявка
    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): void
    {
        $this->ticket = $ticket;
    }

    // Пользователь
    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    // Содержание комментария
    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    // Дата создания
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
