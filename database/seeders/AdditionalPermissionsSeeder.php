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
            
            // Gérer catégories formations (missing in PermissionSeeder)
            ['name' => 'Gérer catégories formations', 'slug' => 'gerer_categories_formations', 'module' => 'formations', 'action' => 'manage_categories', 'order' => 75],
        ];
        
        foreach ($additionalPermissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}