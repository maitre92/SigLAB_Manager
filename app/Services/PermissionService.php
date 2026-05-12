<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service pour la gestion des permissions
 */
class PermissionService
{
    /**
     * Créer une nouvelle permission
     */
    public function create(array $data): Permission
    {
        $permission = Permission::create($data);

        ActivityLog::log(
            action: 'create_permission',
            subject: 'Permission',
            subjectId: $permission->id,
            description: "La permission '{$permission->name}' a été créée"
        );

        return $permission;
    }

    /**
     * Mettre à jour une permission
     */
    public function update(Permission $permission, array $data): bool
    {
        $changes = [];

        foreach ($data as $key => $value) {
            if ($permission->$key != $value) {
                $changes[$key] = [
                    'old' => $permission->$key,
                    'new' => $value,
                ];
            }
        }

        $result = $permission->update($data);

        if ($result && !empty($changes)) {
            ActivityLog::log(
                action: 'update_permission',
                subject: 'Permission',
                subjectId: $permission->id,
                description: "La permission '{$permission->name}' a été modifiée",
                changes: $changes
            );
        }

        return $result;
    }

    /**
     * Supprimer une permission
     */
    public function delete(Permission $permission): bool
    {
        $result = $permission->delete();

        if ($result) {
            ActivityLog::log(
                action: 'delete_permission',
                subject: 'Permission',
                subjectId: $permission->id,
                description: "La permission '{$permission->name}' a été supprimée"
            );
        }

        return $result;
    }

    /**
     * Récupérer toutes les permissions
     */
    public function getAll(): Collection
    {
        return Permission::active()->ordered()->get();
    }

    /**
     * Récupérer les permissions groupées par module
     */
    public function groupedByModule(): Collection
    {
        return Permission::groupedByModule();
    }

    /**
     * Récupérer les permissions d'un module
     */
    public function getByModule(string $module): Collection
    {
        return Permission::forModule($module);
    }

    /**
     * Trouver une permission par slug
     */
    public function findBySlug(string $slug): ?Permission
    {
        return Permission::where('slug', $slug)->first();
    }

    /**
     * Trouver une permission par ID
     */
    public function find(int $id): ?Permission
    {
        return Permission::find($id);
    }

    /**
     * Vérifier si une permission existe
     */
    public function exists(string $slug): bool
    {
        return Permission::where('slug', $slug)->exists();
    }

    /**
     * Créer les permissions par défaut
     */
    public function createDefaultPermissions(): void
    {
        $permissions = [
            // Gestion des utilisateurs
            ['name' => 'Voir les utilisateurs', 'slug' => 'view_users', 'module' => 'users', 'action' => 'view'],
            ['name' => 'Ajouter un utilisateur', 'slug' => 'create_user', 'module' => 'users', 'action' => 'create'],
            ['name' => 'Modifier un utilisateur', 'slug' => 'edit_user', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'Supprimer un utilisateur', 'slug' => 'delete_user', 'module' => 'users', 'action' => 'delete'],

            // Gestion des formations
            ['name' => 'Voir les formations', 'slug' => 'view_formations', 'module' => 'formations', 'action' => 'view'],
            ['name' => 'Ajouter une formation', 'slug' => 'create_formation', 'module' => 'formations', 'action' => 'create'],
            ['name' => 'Modifier une formation', 'slug' => 'edit_formation', 'module' => 'formations', 'action' => 'edit'],
            ['name' => 'Supprimer une formation', 'slug' => 'delete_formation', 'module' => 'formations', 'action' => 'delete'],

            // Gestion financière
            ['name' => 'Voir les finances', 'slug' => 'view_finances', 'module' => 'finances', 'action' => 'view'],
            ['name' => 'Ajouter une transaction', 'slug' => 'create_transaction', 'module' => 'finances', 'action' => 'create'],
            ['name' => 'Modifier une transaction', 'slug' => 'edit_transaction', 'module' => 'finances', 'action' => 'edit'],
            ['name' => 'Supprimer une transaction', 'slug' => 'delete_transaction', 'module' => 'finances', 'action' => 'delete'],

            // Gestion des attestations
            ['name' => 'Voir les attestations', 'slug' => 'view_attestations', 'module' => 'attestations', 'action' => 'view'],
            ['name' => 'Créer une attestation', 'slug' => 'create_attestation', 'module' => 'attestations', 'action' => 'create'],

            // Gestion des emplois du temps
            ['name' => 'Voir les emplois du temps', 'slug' => 'view_schedules', 'module' => 'schedules', 'action' => 'view'],
            ['name' => 'Gérer les emplois du temps', 'slug' => 'manage_schedules', 'module' => 'schedules', 'action' => 'edit'],

            // Gestion des évaluations
            ['name' => 'Voir les évaluations', 'slug' => 'view_evaluations', 'module' => 'evaluations', 'action' => 'view'],
            ['name' => 'Créer une évaluation', 'slug' => 'create_evaluation', 'module' => 'evaluations', 'action' => 'create'],

            // Statistiques et rapports
            ['name' => 'Voir les statistiques', 'slug' => 'view_statistics', 'module' => 'statistics', 'action' => 'view'],
            ['name' => 'Voir les rapports', 'slug' => 'view_reports', 'module' => 'reports', 'action' => 'view'],

            // Gestion documentaire
            ['name' => 'Voir les documents', 'slug' => 'view_documents', 'module' => 'documents', 'action' => 'view'],
            ['name' => 'Ajouter un document', 'slug' => 'create_document', 'module' => 'documents', 'action' => 'create'],

            // Gestion des permissions
            ['name' => 'Voir les permissions', 'slug' => 'view_permissions', 'module' => 'permissions', 'action' => 'view'],
            ['name' => 'Gérer les permissions', 'slug' => 'manage_permissions', 'module' => 'permissions', 'action' => 'edit'],

            // Système
            ['name' => 'Accéder au tableau de bord', 'slug' => 'access_dashboard', 'module' => 'dashboard', 'action' => 'view'],
            ['name' => 'Voir les logs d\'activité', 'slug' => 'view_activity_logs', 'module' => 'system', 'action' => 'view'],
        ];

        foreach ($permissions as $permission) {
            if (!$this->exists($permission['slug'])) {
                $this->create($permission);
            }
        }
    }

    /**
     * Obtenir les modules disponibles avec leurs permissions
     */
    public function getModulesWithPermissions(): array
    {
        $modules = [];
        $grouped = $this->groupedByModule();

        foreach ($grouped as $module => $permissions) {
            $modules[$module] = [
                'permissions' => $permissions->keyBy('action'),
                'count' => $permissions->count(),
            ];
        }

        return $modules;
    }

    /**
     * Récupérer les permissions pour un utilisateur donnécomme un tableau d'administration
     */
    public function getAdminGrid(int $userId = null)
    {
        $modules = $this->getModulesWithPermissions();
        $userPermissions = [];

        if ($userId) {
            $user = \App\Models\User::find($userId);
            if ($user) {
                $userPermissions = $user->permissions()->pluck('id')->toArray();
            }
        }

        return [
            'modules' => $modules,
            'userPermissions' => $userPermissions,
        ];
    }
}
