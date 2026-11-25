<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;


final class TicketVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const CHANGE_STATUS = 'CHANGE_STATUS';
    public const DELETE = 'DELETE';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager
    ) {}

    protected function supports(
        string $attribute,
        mixed $subject
    ): bool {
        return $subject instanceof Ticket
            && in_array($attribute, [
                self::VIEW,
                self::EDIT,
                self::CHANGE_STATUS,
                self::DELETE,
            ], true);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        // Гостям и заблокированным пользователям доступ запрещён
        if (!$user instanceof User || !$user->isActive()) {
            return false;
        }

        // Удалять заявки может только администратор
        if ($attribute === self::DELETE) {
            return $this->accessDecisionManager->decide(
                $token,
                ['ROLE_ADMIN']
            );
        }

        // Менеджер и администратор могут выполнять
        // остальные действия с любой заявкой
        if ($this->accessDecisionManager->decide(
            $token,
            ['ROLE_MANAGER']
        )) {
            return true;
        }

        $userId = $user->getId();

        if ($userId === null) {
            return false;
        }

        // Проверяем автора заявки
        $isOwner = $subject->getCreatedBy()->getId() === $userId;

        // Проверяем назначенного исполнителя
        $assignedTo = $subject->getAssignedTo();

        $isAssigned = $assignedTo !== null
            && $assignedTo->getId() === $userId;

        // Специалист технической поддержки
        if ($this->accessDecisionManager->decide(
            $token,
            ['ROLE_TECHNICIAN']
        )) {
            return match ($attribute) {
                self::VIEW => $isOwner || $isAssigned,

                self::EDIT,
                self::CHANGE_STATUS => $isAssigned,

                default => false,
            };
        }

        // Обычный пользователь может смотреть только свои заявки
        return $attribute === self::VIEW && $isOwner;
    }
}
