<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Utilisateurs
            ['name' => 'Voir les utilisateurs', 'module' => 'Utilisateurs', 'slug' => 'view_users'],
            ['name' => 'Ajouter un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'create_user'],
            ['name' => 'Modifier un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'edit_user'],
            ['name' => 'Supprimer un utilisateur', 'module' => 'Utilisateurs', 'slug' => 'delete_user'],

            // Permissions
            ['name' => 'Voir les permissions', 'module' => 'Permissions', 'slug' => 'view_permissions'],
            ['name' => 'Gérer les permissions', 'module' => 'Permissions', 'slug' => 'manage_permissions'],
            ['name' => 'Ajouter une permission', 'module' => 'Permissions', 'slug' => 'create_permission'],
            ['name' => 'Supprimer une permission', 'module' => 'Permissions', 'slug' => 'delete_permission'],

            // Apprenants
            ['name' => 'Voir les apprenants', 'module' => 'Apprenants', 'slug' => 'view_learners'],
            ['name' => 'Ajouter un apprenant', 'module' => 'Apprenants', 'slug' => 'create_learner'],
            ['name' => 'Modifier un apprenant', 'module' => 'Apprenants', 'slug' => 'edit_learner'],
            ['name' => 'Supprimer un apprenant', 'module' => 'Apprenants', 'slug' => 'delete_learner'],
            ['name' => 'Voir les détails apprenant', 'module' => 'Apprenants', 'slug' => 'view_learner_details'],

            // Formations
            ['name' => 'Voir les formations', 'module' => 'Formations', 'slug' => 'view_courses'],
            ['name' => 'Ajouter une formation', 'module' => 'Formations', 'slug' => 'create_course'],
            ['name' => 'Voir les formations', 'module' => 'Formations', 'slug' => 'voir_formations', 'action' => 'view', 'order' => 30],
            ['name' => 'Ajouter une formation', 'module' => 'Formations', 'slug' => 'ajouter_formation', 'action' => 'create', 'order' => 31],
            ['name' => 'Modifier une formation', 'module' => 'Formations', 'slug' => 'modifier_formation', 'action' => 'edit', 'order' => 32],
            ['name' => 'Supprimer une formation', 'module' => 'Formations', 'slug' => 'supprimer_formation', 'action' => 'delete', 'order' => 33],
            ['name' => 'Voir les détails formation', 'module' => 'Formations', 'slug' => 'voir_details_formation', 'action' => 'view_details', 'order' => 34],

            // Pédagogique
            ['name' => 'Voir le module pédagogique', 'module' => 'Pédagogique', 'slug' => 'view_pedagogical'],
            ['name' => 'Voir les présences', 'module' => 'Présences', 'slug' => 'view_attendance'],
            ['name' => 'Voir les évaluations', 'module' => 'Évaluations', 'slug' => 'view_evaluations'],
            ['name' => 'Voir les examens', 'module' => 'Examens', 'slug' => 'view_exams'],
            ['name' => 'Voir les notes', 'module' => 'Notes', 'slug' => 'view_grades'],

            // Emplois du temps
            ['name' => 'Voir les emplois du temps', 'module' => 'Emplois du Temps', 'slug' => 'view_schedules'],

            // Finances
            ['name' => 'Voir les finances', 'module' => 'Finances', 'slug' => 'view_finances'],
            ['name' => 'Voir les paiements', 'module' => 'Paiements', 'slug' => 'view_payments'],
            ['name' => 'Voir les dépenses', 'module' => 'Dépenses', 'slug' => 'view_expenses'],
            ['name' => 'Voir les recettes', 'module' => 'Recettes', 'slug' => 'view_revenue'],

            // Attestations
            ['name' => 'Voir les attestations', 'module' => 'Attestations', 'slug' => 'view_certificates'],

            // Documents
            ['name' => 'Voir les documents', 'module' => 'Documents', 'slug' => 'view_documents'],

            // Rapports
            ['name' => 'Voir les rapports', 'module' => 'Rapports', 'slug' => 'view_reports'],

            // Traçabilité
            ['name' => 'Voir la traçabilité', 'module' => 'Traçabilité', 'slug' => 'view_audit'],

            // Paramètres
            ['name' => 'manage_settings', 'module' => 'Paramètres', 'slug' => 'manage_settings'],
        ];

        foreach ($permissions as $permission) {
            $existing = Permission::withTrashed()->where('slug', $permission['slug'])->first();
            
            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->update($permission);
            } else {
                Permission::create($permission);
            }
        }
    }
}
