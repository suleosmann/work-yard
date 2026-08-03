<?php

namespace Database\Seeders;

use App\Support\Permission as PermissionName;
use App\Support\Role as RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionName::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => RoleName::ADMIN]);
        $admin->syncPermissions(PermissionName::all());

        $pm = Role::firstOrCreate(['name' => RoleName::PM]);
        $pm->syncPermissions([
            PermissionName::CREATE_PROJECTS,
            PermissionName::EDIT_PROJECTS,
            PermissionName::CREATE_TASKS,
            PermissionName::ASSIGN_TASKS,
            PermissionName::EDIT_TASK_FIELDS,
            PermissionName::DELETE_TASKS,
            PermissionName::ADVANCE_TASK_STATUS,
            PermissionName::MANAGE_RESOURCES,
            PermissionName::ADD_COMMENTS,
        ]);

        $developer = Role::firstOrCreate(['name' => RoleName::DEVELOPER]);
        $developer->syncPermissions([
            PermissionName::ADVANCE_TASK_STATUS,
            PermissionName::ADD_COMMENTS,
        ]);

        $qa = Role::firstOrCreate(['name' => RoleName::QA]);
        $qa->syncPermissions([
            PermissionName::QA_DECIDE,
            PermissionName::ADD_COMMENTS,
        ]);
    }
}
