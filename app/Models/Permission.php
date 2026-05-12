<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module',
        'action',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Récupérer tous les utilisateurs qui possèdent cette permission
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions')
            ->withTimestamps()
            ->withPivot('granted_by', 'reason', 'granted_at');
    }

    /**
     * Récupérer l'utilisateur qui a accordé cette permission
     */
    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    /**
     * Scopes pour les requêtes courantes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('module')->orderBy('order');
    }

    /**
     * Récupérer toutes les permissions groupées par module
     */
    public static function groupedByModule()
    {
        return static::active()
            ->ordered()
            ->get()
            ->groupBy('module');
    }

    /**
     * Récupérer les permissions pour un module donné
     */
    public static function forModule(string $module)
    {
        return static::byModule($module)->active()->get();
    }
}
