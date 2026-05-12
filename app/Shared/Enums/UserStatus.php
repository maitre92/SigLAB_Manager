<?php

namespace App\Shared\Enums;

/**
 * Énumération des statuts utilisateur
 */
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
    case BANNED = 'banned';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::INACTIVE => 'Inactif',
            self::PENDING => 'En attente',
            self::SUSPENDED => 'Suspendu',
            self::BANNED => 'Banni',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'secondary',
            self::PENDING => 'warning',
            self::SUSPENDED => 'danger',
            self::BANNED => 'dark',
        };
    }
}
