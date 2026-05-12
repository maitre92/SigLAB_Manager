{{-- database/seeders/AdditionalPermissionsSeeder.php --}}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class AdditionalPermissionsSeeder extends Seeder
{
    public function run()
    {
        $additionalPermissions = [
            // Dashboard
            ['name' => 'Accéder au tableau de bord', 'slug' => 'access_dashboard', 'module' => 'dashboard', 'action' => 'view', 'order' => 1],
            
            // Gestion des permissions
            ['name' => 'Voir les permissions', 'slug' => 'view_permissions', 'module' => 'permissions', 'action' => 'view', 'order' => 10],
            ['name' => 'Gérer les permissions', 'slug' => 'manage_permissions', 'module' => 'permissions', 'action' => 'manage', 'order' => 11],
            
            // Apprenants
            ['name' => 'Voir les apprenants', 'slug' => 'view_students', 'module' => 'students', 'action' => 'view', 'order' => 20],
            ['name' => 'Ajouter un apprenant', 'slug' => 'create_student', 'module' => 'students', 'action' => 'create', 'order' => 21],
            ['name' => 'Modifier un apprenant', 'slug' => 'edit_student', 'module' => 'students', 'action' => 'edit', 'order' => 22],
            ['name' => 'Supprimer un apprenant', 'slug' => 'delete_student', 'module' => 'students', 'action' => 'delete', 'order' => 23],
            
            // Évaluations
            ['name' => 'Voir les évaluations', 'slug' => 'view_evaluations', 'module' => 'evaluations', 'action' => 'view', 'order' => 30],
            ['name' => 'Créer une évaluation', 'slug' => 'create_evaluation', 'module' => 'evaluations', 'action' => 'create', 'order' => 31],
            
            // Emplois du temps
            ['name' => 'Voir les emplois du temps', 'slug' => 'view_schedules', 'module' => 'schedules', 'action' => 'view', 'order' => 40],
            ['name' => 'Gérer les emplois du temps', 'slug' => 'manage_schedules', 'module' => 'schedules', 'action' => 'manage', 'order' => 41],
            
            // Attestations
            ['name' => 'Voir les attestations', 'slug' => 'view_attestations', 'module' => 'attestations', 'action' => 'view', 'order' => 50],
            ['name' => 'Créer une attestation', 'slug' => 'create_attestation', 'module' => 'attestations', 'action' => 'create', 'order' => 51],
            
            // Paramètres
            ['name' => 'Voir les paramètres', 'slug' => 'view_settings', 'module' => 'settings', 'action' => 'view', 'order' => 60],
            ['name' => 'Gérer les paramètres', 'slug' => 'manage_settings', 'module' => 'settings', 'action' => 'manage', 'order' => 61],
        ];
        
        foreach ($additionalPermissions as $permission) {
            if (!Permission::where('slug', $permission['slug'])->exists()) {
                Permission::create($permission);
            }
        }
    }
}