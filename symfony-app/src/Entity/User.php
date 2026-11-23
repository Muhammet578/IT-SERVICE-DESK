<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        type: 'integer',
        options: ['unsigned' => true]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $name;

    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column(
        name: 'is_active',
        type: 'boolean',
        options: ['default' => true]
    )]
    private bool $isActive = true;

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

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(
        name: 'role_id',
        referencedColumnName: 'id',
        nullable: false
    )]
    private Role $role;

    // Конструктор
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    
    public function getPasswordHash(): string
    {
        return $this->password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->password = $passwordHash;
    }
    public function eraseCredentials(): void
    {
        // Открытый пароль в сущности не хранится.
    }

    
    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getRoles(): array
    {
        $roleName = strtoupper($this->role->getName());

        if (!str_starts_with($roleName, 'ROLE_')) {
            $roleName = 'ROLE_' . $roleName;
        }

        return [$roleName];
    }
    
    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
