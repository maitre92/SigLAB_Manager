<?php

namespace App\Shared\Enums;

enum FormationStatut: string
{
    case PLANIFIEE = 'planifiee';
    case EN_COURS = 'en_cours';
    case TERMINEE = 'terminee';
    case SUSPENDUE = 'suspendue';

    public function label(): string
    {
        return match ($this) {
            self::PLANIFIEE => 'Planifiée',
            self::EN_COURS => 'En cours',
            self::TERMINEE => 'Terminée',
            self::SUSPENDUE => 'Suspendue',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PLANIFIEE => 'primary',
            self::EN_COURS => 'success',
            self::TERMINEE => 'secondary',
            self::SUSPENDUE => 'warning',
        };
    }
}
