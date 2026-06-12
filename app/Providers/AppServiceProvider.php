<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Database\Seeders\PermissionSeeder;
use App\Models\SuiviNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer les gates dynamiquement pour les permissions
        Gate::define('manage-settings', function (User $user) {
            return $user->isSuperAdmin() || $user->hasPermission('manage_settings');
        });

        $this->ensureDefaultPermissions();

        // Définir dynamiquement les gates pour chaque permission active en base
        if ($this->hasTableSafely('permissions')) {
            $permissionSlugs = Permission::where('is_active', true)->pluck('slug')->toArray();
            foreach ($permissionSlugs as $permission) {
                Gate::define($permission, function (User $user) use ($permission) {
                    return $user->isSuperAdmin() || $user->hasPermission($permission);
                });
            }
        }

        View::composer('layouts.navbar', function ($view) {
            $notifications = collect();
            $notificationsCount = 0;

            if (auth()->check() && $this->hasTableSafely('suivi_notifications')) {
                $baseQuery = SuiviNotification::where('user_id', auth()->id())
                    ->whereNull('read_at');

                $notificationsCount = (clone $baseQuery)->count();
                $notifications = $baseQuery
                    ->with('emargement.groupeFormation')
                    ->latest()
                    ->take(5)
                    ->get();
            }

            $view->with('suiviNotifications', $notifications)
                ->with('suiviNotificationsCount', $notificationsCount);
        });
    }

    private function ensureDefaultPermissions(): void
    {
        if (!$this->hasTableSafely('permissions') || !$this->hasTableSafely('users') || !$this->hasTableSafely('user_permissions')) {
            return;
        }

        if (Permission::count() === 0) {
            (new PermissionSeeder())->run();
        }

        $permissionSlugs = Permission::where('is_active', true)->pluck('slug')->toArray();
        if (empty($permissionSlugs)) {
            return;
        }

        $superAdmins = User::where('role', UserRole::SUPERADMIN->value)->get();

        // Si aucun superadmin n'existe, créer deux comptes par défaut à partir des variables d'environnement
        if ($superAdmins->count() === 0) {
            $defaults = [
                [
                    'name' => env('DEFAULT_SUPERADMIN_NAME_1', 'Barry Moustapha'),
                    'email' => env('DEFAULT_SUPERADMIN_EMAIL_1', 'barrymoustapha485@gmail.com'),
                    'password' => env('DEFAULT_SUPERADMIN_PASSWORD_1', 'superadmin123'),
                ],
                [
                    'name' => env('DEFAULT_SUPERADMIN_NAME_2', 'Oumar Ouolo'),
                    'email' => env('DEFAULT_SUPERADMIN_EMAIL_2', 'oumarouolo2023@gmail.com'),
                    'password' => env('DEFAULT_SUPERADMIN_PASSWORD_2', 'superadmin123'),
                ],
            ];

            foreach ($defaults as $d) {
                try {
                    $user = User::firstOrCreate(
                        ['email' => $d['email']],
                        [
                            'name' => $d['name'],
                            'password' => Hash::make($d['password']),
                            'role' => UserRole::SUPERADMIN->value,
                            'is_active' => true,
                            'status' => null,
                        ]
                    );

                    // S'assurer que le rôle est bien superadmin (au cas où il existait déjà avec un autre rôle)
                    if ($user->role !== UserRole::SUPERADMIN->value) {
                        $user->role = UserRole::SUPERADMIN->value;
                        $user->save();
                    }

                    // Attribuer les permissions initiales
                    $user->grantPermissions($permissionSlugs, 'Création automatique du superadmin');
                } catch (\Exception $ex) {
                    Log::warning('Impossible de créer le superadmin par défaut: ' . $ex->getMessage());
                }
            }
        } else {
            foreach ($superAdmins as $superAdmin) {
                $superAdmin->grantPermissions($permissionSlugs, 'Initialisation automatique des permissions pour superadmin');
            }
        }

        // Les permissions des administrateurs standards doivent rester explicites.
        // Seuls les superadmins reçoivent automatiquement toutes les permissions.
    }

    private function hasTableSafely(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $exception) {
            Log::warning("Impossible de vérifier la table {$table}: " . $exception->getMessage());
            return false;
        }
    }
}
