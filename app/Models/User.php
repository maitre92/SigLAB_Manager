<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Shared\Traits\HasPermissions;
use App\Shared\Enums\UserRole;

/**
 * @method bool hasPermission(string $permission)
 * @method bool hasAnyPermission(array $permissions)
 * @method bool hasAllPermissions(array $permissions)
 * @method void grantPermission($permission, ?string $reason = null)
 * @method void revokePermission($permission)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPermissions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Enregistrer la connexion de l'utilisateur
     */
    public function recordLogin(): void
    {
        $this->last_login_at = now();
        $this->last_login_ip = request()?->ip();
        $this->save();
    }

    /**
     * Enregistrer la déconnexion de l'utilisateur
     */
    public function recordLogout(): void
    {
        // Optionnel: conserver la date de dernière déconnexion si nécessaire
        $this->save();
    }

    /**
     * Vérifier si l'utilisateur est superadmin
     */
    public function isSuperAdmin(): bool
    {
        return isset($this->role) && $this->role === UserRole::SUPERADMIN->value;
    }

    /**
     * Vérifier si l'utilisateur a les droits d'administrateur
     */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->hasAnyActivePermission();
    }

    /**
     * Vérifier si l'utilisateur possède au moins une permission active
     */
    public function hasAnyActivePermission(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissions()
            ->where('is_active', true)
            ->exists();
    }
}
