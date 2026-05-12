<?php

namespace App\Shared\Enums;

/**
 * Énumération des statuts d'un apprenant
 */
enum ApprenantStatut: string
{
    case ACTIF = 'actif';
    case INACTIF = 'inactif';
    case SUSPENDU = 'suspendu';
    case DIPLOME = 'diplome';
    case ABANDONNE = 'abandonne';

    public function label(): string
    {
        return match ($this) {
            self::ACTIF => 'Actif',
            self::INACTIF => 'Inactif',
            self::SUSPENDU => 'Suspendu',
            self::DIPLOME => 'Diplômé',
            self::ABANDONNE => 'Abandonné',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIF => 'success',
            self::INACTIF => 'secondary',
            self::SUSPENDU => 'warning',
            self::DIPLOME => 'info',
            self::ABANDONNE => 'danger',
        };
    }
}
