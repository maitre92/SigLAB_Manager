<?php

namespace App\Shared\Enums;

/**
 * Énumération des rôles utilisateur
 */
enum UserRole: string
{
    case SUPERADMIN = 'superadmin';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case USER = 'user';
    case GUEST = 'guest';
    case FORMATEUR = 'formateur';
    case PERSONNEL_ADMINISTRATIF = 'personnel_administratif';
    case COMPTABLE = 'comptable';
    case DIRECTEUR = 'directeur';

    public function label(): string
    {
        return match ($this) {
            self::SUPERADMIN => 'Super Administrateur',
            self::ADMIN => 'Administrateur',
            self::MANAGER => 'Gestionnaire',
            self::USER => 'Utilisateur',
            self::GUEST => 'Invité',
            self::FORMATEUR => 'Formateur',
            self::PERSONNEL_ADMINISTRATIF => 'Personnel Administratif',
            self::COMPTABLE => 'Comptable',
            self::DIRECTEUR => 'Directeur',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SUPERADMIN => ['*'],
            self::ADMIN => \App\Services\RolePermissionService::permissionsForRole($this),
            self::MANAGER => \App\Services\RolePermissionService::permissionsForRole($this),
            self::USER => \App\Services\RolePermissionService::permissionsForRole($this),
            self::GUEST => [],
            self::FORMATEUR => \App\Services\RolePermissionService::permissionsForRole($this),
            self::PERSONNEL_ADMINISTRATIF => \App\Services\RolePermissionService::permissionsForRole($this),
            self::COMPTABLE => \App\Services\RolePermissionService::permissionsForRole($this),
            self::DIRECTEUR => \App\Services\RolePermissionService::permissionsForRole($this),
        };
    }

    public function level(): int
    {
        return match ($this) {
            self::SUPERADMIN => 5,
            self::ADMIN => 4,
            self::MANAGER => 3,
            self::USER => 2,
            self::GUEST => 1,
            self::DIRECTEUR => 4,
            self::COMPTABLE => 3,
            self::PERSONNEL_ADMINISTRATIF => 3,
            self::FORMATEUR => 2,
        };
    }

    public function canManage(UserRole $other): bool
    {
        return $this->level() > $other->level();
    }

    public static function visibleBy(?\App\Models\User $user): array
    {
        if (!$user) {
            return [];
        }

        $currentRole = self::tryFrom($user->role);
        if (!$currentRole) {
            return [];
        }

        if ($currentRole === self::SUPERADMIN) {
            return self::cases();
        }

        return array_values(array_filter(
            self::cases(),
            fn (self $role) => $currentRole->canManage($role)
        ));
    }

    public static function assignableBy(?\App\Models\User $user): array
    {
        return array_values(array_filter(
            self::visibleBy($user),
            fn (self $role) => !in_array($role, [self::USER, self::GUEST], true)
        ));
    }
}
