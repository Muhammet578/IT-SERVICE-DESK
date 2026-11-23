<?php

declare(strict_types=1);

namespace App\Entity;
use App\Repository\TicketRepository;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
#[ORM\Index(name: 'idx_tickets_created_by', columns: ['created_by'])]
#[ORM\Index(name: 'idx_tickets_assigned_to', columns: ['assigned_to'])]
#[ORM\Index(name: 'fk_tickets_category', columns: ['category_id'])]
#[ORM\Index(name: 'fk_tickets_priority', columns: ['priority_id'])]
#[ORM\Index(name: 'idx_tickets_status', columns: ['status_id'])]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        type: 'integer',
        options: ['unsigned' => true]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    // Автор заявки
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: 'created_by',
        referencedColumnName: 'id',
        nullable: false
    )]
    private User $createdBy;

    // Исполнитель заявки
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(
        name: 'assigned_to',
        referencedColumnName: 'id',
        nullable: true,
        onDelete: 'SET NULL'
    )]
    private ?User $assignedTo = null;

    // Категория
    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(
        name: 'category_id',
        referencedColumnName: 'id',
        nullable: false
    )]
    private Category $category;

    // Приоритет
    #[ORM\ManyToOne(targetEntity: Priority::class)]
    #[ORM\JoinColumn(
        name: 'priority_id',
        referencedColumnName: 'id',
        nullable: false
    )]
    private Priority $priority;

    // Статус
    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(
        name: 'status_id',
        referencedColumnName: 'id',
        nullable: false
    )]
    private Status $status;

    // Даты
    #[ORM\Column(
        name: 'created_at',
        type: 'datetime_immutable'
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(
        name: 'updated_at',
        type: 'datetime_immutable'
    )]
    private \DateTimeImmutable $updatedAt;

    // Конструктор
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Название
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    // Описание
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    // Автор заявки
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $user): void
    {
        $this->createdBy = $user;
    }

    // Исполнитель
    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $user): void
    {
        $this->assignedTo = $user;
    }

    // Категория
    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    // Приоритет
    public function getPriority(): Priority
    {
        return $this->priority;
    }

    public function setPriority(Priority $priority): void
    {
        $this->priority = $priority;
    }

    // Статус
    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    // Дата создания
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    // Дата изменения
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(
        \DateTimeImmutable $updatedAt
    ): void {
        $this->updatedAt = $updatedAt;
    }
}
