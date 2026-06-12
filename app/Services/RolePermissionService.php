<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use App\Shared\Enums\UserRole;

class RolePermissionService
{
    public static function permissionsForRole(string|UserRole|null $role): array
    {
        $roleValue = $role instanceof UserRole ? $role->value : (string) $role;

        return match ($roleValue) {
            UserRole::SUPERADMIN->value => ['*'],

            UserRole::ADMIN->value => [
                'view_users', 'create_user', 'edit_user',
                'view_learners', 'create_learner', 'edit_learner', 'delete_learner', 'view_learner_details',
                'voir_formations', 'ajouter_formation', 'modifier_formation', 'voir_details_formation',
                'voir_categories_formations', 'ajouter_categorie_formation', 'modifier_categorie_formation', 'gerer_categories_formations',
                'view_pedagogical', 'view_attendance', 'view_evaluations', 'view_exams', 'view_grades',
                'view_suivi_pedagogique', 'validate_emargement', 'view_emargement_reports',
                'view_schedules', 'view_finances', 'view_payments', 'view_expenses', 'view_revenue',
                'edit_payment', 'edit_expense', 'edit_trainer_payment',
                'view_certificates', 'view_reports', 'view_movements',
                'manage_settings',
            ],

            UserRole::DIRECTEUR->value => [
                'view_users', 'view_learners', 'view_learner_details',
                'voir_formations', 'voir_details_formation', 'voir_categories_formations',
                'view_pedagogical', 'view_attendance', 'view_evaluations', 'view_exams', 'view_grades',
                'view_suivi_pedagogique', 'validate_emargement', 'view_emargement_reports',
                'view_finances', 'view_payments', 'view_expenses', 'view_revenue',
                'view_certificates', 'view_reports', 'view_movements',
            ],

            UserRole::MANAGER->value => [
                'view_users', 'create_user', 'edit_user',
                'view_learners', 'create_learner', 'edit_learner', 'view_learner_details',
                'voir_formations', 'ajouter_formation', 'modifier_formation', 'voir_details_formation',
                'voir_categories_formations', 'gerer_categories_formations',
                'view_pedagogical', 'view_attendance', 'view_evaluations', 'view_exams', 'view_grades',
                'view_suivi_pedagogique', 'validate_emargement', 'view_emargement_reports',
                'view_schedules', 'view_certificates', 'view_movements',
            ],

            UserRole::PERSONNEL_ADMINISTRATIF->value => [
                'view_learners', 'create_learner', 'edit_learner', 'view_learner_details',
                'voir_formations', 'voir_details_formation', 'voir_categories_formations',
                'view_pedagogical', 'view_attendance', 'view_evaluations', 'view_exams', 'view_grades',
                'view_certificates', 'view_schedules',
            ],

            UserRole::COMPTABLE->value => [
                'view_learners', 'view_learner_details',
                'voir_formations', 'voir_details_formation',
                'view_finances', 'view_payments', 'view_expenses', 'view_revenue',
                'edit_payment', 'delete_payment', 'edit_expense', 'delete_expense',
                'edit_trainer_payment', 'delete_trainer_payment',
                'view_suivi_pedagogique', 'view_emargement_reports',
            ],

            UserRole::FORMATEUR->value => [
                'view_pedagogical', 'view_suivi_pedagogique', 'create_emargement', 'view_schedules',
            ],

            UserRole::USER->value => [
                'view_suivi_pedagogique',
            ],

            default => [],
        };
    }

    public function syncUserRolePermissions(User $user, bool $replace = false): void
    {
        $slugs = self::permissionsForRole($user->role);

        if (in_array('*', $slugs, true)) {
            $slugs = Permission::where('is_active', true)->pluck('slug')->all();
        }

        $permissionIds = Permission::whereIn('slug', $slugs)
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $syncData = collect($permissionIds)
            ->mapWithKeys(fn($permissionId) => [
                $permissionId => [
                    'granted_by' => auth()->id(),
                    'reason' => 'Permissions par défaut du rôle',
                    'granted_at' => now(),
                ],
            ])
            ->all();

        if ($replace) {
            $user->permissions()->sync($syncData);
            return;
        }

        $user->permissions()->syncWithoutDetaching($syncData);
    }
}
