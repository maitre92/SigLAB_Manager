<?php

namespace App\Shared\Enums;

/**
 * Énumération des niveaux d'étude
 */
enum NiveauEtude: string
{
    case AUCUN = 'aucun';
    case PRIMAIRE = 'primaire';
    case SECONDAIRE = 'secondaire';
    case BACCALAUREAT = 'baccalaureat';
    case LICENCE = 'licence';
    case MASTER = 'master';
    case DOCTORAT = 'doctorat';
    case AUTRE = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::AUCUN => 'Aucun',
            self::PRIMAIRE => 'Primaire',
            self::SECONDAIRE => 'Secondaire',
            self::BACCALAUREAT => 'Baccalauréat',
            self::LICENCE => 'Licence',
            self::MASTER => 'Master',
            self::DOCTORAT => 'Doctorat',
            self::AUTRE => 'Autre',
        };
    }
}
