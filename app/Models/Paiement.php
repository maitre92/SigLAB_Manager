<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inscription_id',
        'montant',
        'date_paiement',
        'mode_paiement',
        'reference',
        'recu_numero',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2'
    ];

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateRecuNumero()
    {
        $year = date('Y');
        // Rechercher le dernier numéro de reçu pour l'année en cours
        $lastPaiement = self::withTrashed()
            ->where('recu_numero', 'like', "REC-$year-%")
            ->orderByRaw('CAST(SUBSTRING(recu_numero, 10) AS UNSIGNED) DESC')
            ->first();

        if ($lastPaiement) {
            // Extraire le numéro après "REC-YYYY-"
            $lastNumber = (int) substr($lastPaiement->recu_numero, 9);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $newNumber = 'REC-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        // Vérification finale pour s'assurer que le numéro est bien unique
        while (self::withTrashed()->where('recu_numero', $newNumber)->exists()) {
            $nextNumber++;
            $newNumber = 'REC-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        }

        return $newNumber;
    }
}
