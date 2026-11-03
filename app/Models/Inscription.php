<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'apprenant_id',
        'formation_id',
        'groupe_formation_id',
        'date_inscription',
        'montant_total',
        'montant_paye',
        'montant_remise',
        'statut',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
        'montant_remise' => 'decimal:2',
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class)->withTrashed();
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class)->withTrashed();
    }

    public function groupeFormation()
    {
        return $this->belongsTo(GroupeFormation::class, 'groupe_formation_id')->withTrashed();
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getResteAPayerAttribute()
    {
        return $this->montant_total - $this->montant_paye;
    }

    /**
     * Prix de la formation avant remises (montant_total est le montant net dû).
     */
    public function getMontantBrutAttribute()
    {
        return $this->montant_total + $this->montant_remise;
    }

    public function ajusterRemise(float $variation): void
    {
        if ($variation == 0) {
            return;
        }

        $this->decrement('montant_total', $variation);
        $this->increment('montant_remise', $variation);
    }
}
