<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emargement extends Model
{
    use HasFactory;

    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_VALIDE = 'valide';
    public const STATUT_REJETE = 'rejete';

    protected $fillable = [
        'groupe_formation_id',
        'formateur_id',
        'date_seance',
        'heure_debut',
        'heure_fin',
        'titre_realise',
        'statut',
        'validated_by',
        'validated_at',
        'motif_rejet',
    ];

    protected $casts = [
        'date_seance' => 'date',
        'validated_at' => 'datetime',
    ];

    public function groupeFormation()
    {
        return $this->belongsTo(GroupeFormation::class, 'groupe_formation_id')->withTrashed();
    }

    public function formateur()
    {
        return $this->belongsTo(User::class, 'formateur_id');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function getDureeMinutesAttribute(): int
    {
        $start = Carbon::parse($this->heure_debut);
        $end = Carbon::parse($this->heure_fin);

        return max(0, $start->diffInMinutes($end, false));
    }

    public function getDureeHeuresAttribute(): float
    {
        return round($this->duree_minutes / 60, 2);
    }
}
