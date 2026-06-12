<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(RolePermissionService::class);

        User::query()
            ->whereNotNull('role')
            ->get()
            ->each(fn(User $user) => $service->syncUserRolePermissions($user, true));
    }
}
