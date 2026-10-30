<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupeFormation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'groupes_formation';

    protected $fillable = [
        'formation_id',
        'nom',
        'code',
        'formateur_principal_id',
        'statut',
        'capacite_max',
        'salle',
        'date_debut',
        'date_fin',
        'emploi_du_temps',
        'observations',
        'created_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'capacite_max' => 'integer',
    ];

    public function formation()
    {
        return $this->belongsTo(Formation::class)->withTrashed();
    }

    public function formateurPrincipal()
    {
        return $this->belongsTo(User::class, 'formateur_principal_id');
    }

    public function formateurs()
    {
        return $this->belongsToMany(User::class, 'groupe_formation_formateur', 'groupe_formation_id', 'formateur_id')
            ->withPivot('role', 'taux_commission', 'commission_type', 'montant_commission', 'observations', 'assigned_at')
            ->withTimestamps();
    }

    public function apprenants()
    {
        return $this->belongsToMany(Apprenant::class, 'inscriptions', 'groupe_formation_id', 'apprenant_id')
            ->withPivot('id', 'formation_id', 'date_inscription', 'montant_total', 'montant_paye', 'statut')
            ->withTimestamps();
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'groupe_formation_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class, 'groupe_formation_id');
    }

    public function emargements()
    {
        return $this->hasMany(Emargement::class, 'groupe_formation_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'groupe_formation_id');
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class, 'groupe_formation_id');
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class, 'groupe_formation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    private const JOURS_ISO = [
        'Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4,
        'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7,
    ];

    /**
     * Minutes de cours par jour ISO (1 = lundi) d'après l'emploi du temps.
     */
    public function minutesParJour(): array
    {
        $rows = json_decode($this->emploi_du_temps ?? '', true);
        $minutes = [];

        foreach (is_array($rows) ? $rows : [] as $row) {
            $jour = self::JOURS_ISO[$row['day'] ?? ''] ?? null;
            if (!$jour || empty($row['start']) || empty($row['end'])) {
                continue;
            }

            [$hd, $md] = array_map('intval', explode(':', $row['start']));
            [$hf, $mf] = array_map('intval', explode(':', $row['end']));
            $duree = ($hf * 60 + $mf) - ($hd * 60 + $md);

            if ($duree > 0) {
                $minutes[$jour] = ($minutes[$jour] ?? 0) + $duree;
            }
        }

        return $minutes;
    }

    public function getHeuresHebdomadairesAttribute(): float
    {
        return round(array_sum($this->minutesParJour()) / 60, 2);
    }

    /**
     * Volume horaire prévu du groupe : saisi sur la formation (en heures),
     * ou calculé depuis l'emploi du temps et la période (formation en mois).
     */
    public function getHeuresPrevuesAttribute(): float
    {
        $formation = $this->formation;
        if (!$formation) {
            return 0;
        }

        if ($formation->duree_unite !== 'mois') {
            return (float) $formation->duree_heures;
        }

        $minutesParJour = $this->minutesParJour();
        if (empty($minutesParJour)) {
            return 0;
        }

        $debut = $this->date_debut ?? $formation->date_debut;
        $fin = $this->date_fin
            ?? ($debut && $formation->duree_mois ? $debut->copy()->addMonths($formation->duree_mois)->subDay() : null);

        // Sans période connue : moyenne de 52 semaines / 12 mois
        if (!$debut || !$fin || $fin->lt($debut)) {
            return round(array_sum($minutesParJour) / 60 * ($formation->duree_mois ?? 0) * 52 / 12, 2);
        }

        $total = 0;
        for ($jour = $debut->copy(); $jour->lte($fin); $jour->addDay()) {
            $total += $minutesParJour[$jour->dayOfWeekIso] ?? 0;
        }

        return round($total / 60, 2);
    }

    public function getStatutLabelAttribute()
    {
        $statuts = [
            'planifiee' => 'Planifié',
            'en_cours' => 'En cours',
            'terminee' => 'Terminé',
            'suspendue' => 'Suspendu',
        ];

        return $statuts[$this->statut] ?? $this->statut;
    }
}
