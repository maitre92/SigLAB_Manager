<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'code',
        'description',
        'categorie_formation_id',
        'type',
        'duree_heures',
        'cout',
        'capacite_max',
        'niveau',
        'statut',
        'salle',
        'date_debut',
        'date_fin',
        'emploi_du_temps',
        'created_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'cout' => 'decimal:2',
        'duree_heures' => 'integer',
        'capacite_max' => 'integer',
    ];

    public function categorie()
    {
        return $this->belongsTo(CategorieFormation::class, 'categorie_formation_id');
    }

    public function formateurs()
    {
        return $this->belongsToMany(User::class, 'formation_formateur')
                    ->withPivot('role', 'assigned_at')
                    ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper to get status label (if using strings or statuts table)
    public function getStatutLabelAttribute()
    {
        $statuts = [
            'planifiee' => 'Planifiée',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'suspendue' => 'Suspendue',
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Apprenants inscrits à cette formation
     */
    public function apprenants()
    {
        return $this->belongsToMany(Apprenant::class, 'inscriptions')
            ->withPivot('id', 'date_inscription', 'montant_total', 'montant_paye', 'statut')
            ->withTimestamps();
    }
}
