<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titre',
        'categorie',
        'montant',
        'date_depense',
        'beneficiaire',
        'description',
        'piece_jointe',
        'formation_id',
        'groupe_formation_id',
        'user_id',
        'created_by'
    ];

    protected $casts = [
        'date_depense' => 'date',
        'montant' => 'decimal:2'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function groupeFormation()
    {
        return $this->belongsTo(GroupeFormation::class, 'groupe_formation_id')->withTrashed();
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor pour récupérer dynamiquement le mode de paiement depuis la description
     */
    public function getModePaiementAttribute()
    {
        if (preg_match('/Règlement via\s+([^\s\.]+)/i', $this->description, $matches)) {
            return strtolower($matches[1]);
        }
        return 'espèces';
    }

    /**
     * Accessor pour récupérer dynamiquement la référence de transaction depuis la description
     */
    public function getReferenceAttribute()
    {
        if (preg_match('/\(Réf:\s*([^)]+)\)/i', $this->description, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Accessor pour récupérer dynamiquement les notes depuis la description
     */
    public function getNotesAttribute()
    {
        if (preg_match('/Notes:\s*(.*)$/i', $this->description, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    public static function getCategories()
    {
        return [
            'Salaire',
            'Rémunération Formateur',
            'Loyer',
            'Électricité/Eau',
            'Internet',
            'Marketing/Publicité',
            'Matériel informatique',
            'Fournitures de bureau',
            'Entretien',
            'Impôts/Taxes',
            'Autre'
        ];
    }
}
