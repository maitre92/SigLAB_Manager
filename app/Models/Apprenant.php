<?php

namespace App\Models;

use App\Shared\Enums\ApprenantStatut;
use App\Shared\Enums\NiveauEtude;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apprenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'apprenants';

    protected $fillable = [
        'matricule',
        'prenom',
        'nom',
        'photo',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'telephone',
        'email',
        'adresse',
        'niveau_etude',
        'profession',
        'date_inscription',
        'statut',
        'contact_parent',
        'telephone_parent',
        'observations',
        'created_by',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_inscription' => 'date',
        'statut' => ApprenantStatut::class,
        'niveau_etude' => NiveauEtude::class,
    ];

    /**
     * Boot du modèle : auto-génération du matricule
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Apprenant $apprenant) {
            if (empty($apprenant->matricule)) {
                $apprenant->matricule = static::generateMatricule();
            }
            if (empty($apprenant->created_by) && auth()->check()) {
                $apprenant->created_by = auth()->id();
            }
        });
    }

    /**
     * Générer un matricule unique au format SIG-YYYY-NNNN
     */
    public static function generateMatricule(): string
    {
        $year = date('Y');
        $prefix = "SIG-{$year}-";

        // Trouver le dernier matricule de l'année en cours
        $lastMatricule = static::withTrashed()
            ->where('matricule', 'like', $prefix . '%')
            ->orderBy('matricule', 'desc')
            ->value('matricule');

        if ($lastMatricule) {
            // Extraire le numéro séquentiel
            $lastNumber = (int) substr($lastMatricule, strlen($prefix));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor : nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    /**
     * Accessor : initiales pour l'avatar par défaut
     */
    public function getInitialesAttribute(): string
    {
        $prenom = mb_substr($this->prenom ?? '', 0, 1);
        $nom = mb_substr($this->nom ?? '', 0, 1);
        return mb_strtoupper($prenom . $nom);
    }

    /**
     * Accessor : URL de la photo ou null
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }

    // ─── Relations ───────────────────────────────────────────────

    /**
     * Utilisateur ayant créé l'apprenant
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Formations auxquelles l'apprenant est inscrit
     */
    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'inscriptions')
            ->withPivot('id', 'date_inscription', 'montant_total', 'montant_paye', 'statut')
            ->withTimestamps();
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    /**
     * Notes de l'apprenant
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Présences de l'apprenant
     */
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /**
     * Filtrer les apprenants actifs
     */
    public function scopeActive($query)
    {
        return $query->where('statut', ApprenantStatut::ACTIF->value);
    }

    /**
     * Filtrer par statut
     */
    public function scopeByStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Filtrer par niveau d'étude
     */
    public function scopeByNiveauEtude($query, string $niveau)
    {
        return $query->where('niveau_etude', $niveau);
    }

    /**
     * Recherche multi-champs
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('matricule', 'like', "%{$search}%")
              ->orWhere('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('telephone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhereRaw("CONCAT(prenom, ' ', nom) LIKE ?", ["%{$search}%"]);
        });
    }
}
