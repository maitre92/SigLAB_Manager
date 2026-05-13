<?php

namespace App\Shared\Enums;

enum FormationType: string
{
    case PRESENTIEL = 'presentiel';
    case EN_LIGNE = 'en_ligne';
    case HYBRIDE = 'hybride';

    public function label(): string
    {
        return match ($this) {
            self::PRESENTIEL => 'Présentiel',
            self::EN_LIGNE => 'En ligne',
            self::HYBRIDE => 'Hybride',
        };
    }
}
