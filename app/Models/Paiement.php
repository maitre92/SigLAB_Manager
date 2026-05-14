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
        $lastPaiement = self::withTrashed()->latest()->first();
        $nextId = $lastPaiement ? $lastPaiement->id + 1 : 1;
        return 'REC-' . date('Y') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }
}
