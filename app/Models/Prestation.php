<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prestation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titre',
        'client_nom',
        'client_telephone',
        'description',
        'montant',
        'date_prestation',
        'mode_paiement',
        'reference',
        'recu_numero',
        'statut',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_prestation' => 'date',
        'montant' => 'decimal:2',
    ];

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Génère un numéro de reçu unique pour les prestations
     * Format: PREST-YYYY-XXXXX
     */
    public static function generateRecuNumero()
    {
        $year = date('Y');
        $prefix = "PREST-$year-";
        
        // Rechercher le dernier numéro de reçu pour l'année en cours
        $lastPrestation = self::withTrashed()
            ->where('recu_numero', 'like', "$prefix%")
            ->orderByRaw('CAST(SUBSTRING(recu_numero, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastPrestation) {
            $lastNumber = (int) substr($lastPrestation->recu_numero, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $newNumber = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        // Vérification finale pour s'assurer que le numéro est bien unique
        while (self::withTrashed()->where('recu_numero', $newNumber)->exists()) {
            $nextNumber++;
            $newNumber = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $newNumber;
    }

    /**
     * Liste des statuts disponibles
     */
    public static function getStatuts()
    {
        return [
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'livree' => 'Livrée',
        ];
    }

    /**
     * Accesseur pour le label du statut
     */
    public function getStatutLabelAttribute()
    {
        return self::getStatuts()[$this->statut] ?? $this->statut;
    }

    /**
     * Accesseur pour la couleur CSS du badge de statut
     */
    public function getStatutColorAttribute()
    {
        return match($this->statut) {
            'en_cours' => 'warning',
            'terminee' => 'success',
            'livree' => 'info',
            default => 'secondary',
        };
    }
}
