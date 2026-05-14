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
        'date_inscription',
        'montant_total',
        'montant_paye',
        'statut',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'montant_total' => 'decimal:2',
        'montant_paye' => 'decimal:2',
    ];

    public function apprenant()
    {
        return $this->belongsTo(Apprenant::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
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
}
