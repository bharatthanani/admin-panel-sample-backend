<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Reset cached roles/permissions ──
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Define all permissions ──
        $permissions = [
            // Users
            'users.list', 'users.vendors', 'users.create', 'users.update', 'users.delete',
            // Products
            'products.list', 'products.create', 'products.colors',
            'products.categories', 'products.update', 'products.delete',
            // Categories
            'categories.list', 'categories.create', 'categories.update', 'categories.delete',
            // Stores
            'stores.list', 'stores.create', 'stores.update', 'stores.delete',
            'stores.category', 'stores.products',
            // Roles & Permissions
            'roles.view', 'roles.create', 'roles.update', 'roles.delete',
            'permissions.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        // ── 2. Create roles & assign permissions ──

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $admin->syncPermissions([
            'users.list','users.vendors','users.create','users.update','users.delete',
            'products.list','products.create','products.update','products.delete','products.colors','products.categories',
            'stores.list','stores.create','stores.update','stores.delete','stores.category','stores.products',
            'categories.list','categories.create','categories.update','categories.delete',
            'roles.view','roles.create','roles.update','roles.delete',
            'permissions.view',
        ]);

        $vendor = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'api']);
        $vendor->syncPermissions([
            'products.list','products.create','products.update','products.delete',
            'stores.list','stores.update','stores.products',
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);
        $userRole->syncPermissions([
            'products.list',
            'stores.list',
            'stores.products',
        ]);

        // ── 3. Create demo users ──────────────────────────────────

        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'first_name' => 'Super',
                'last_name'  => 'Admin',
                'password'   => Hash::make('12345'),
                'status'     => 1,
            ]
        );
        $superAdmin->syncPermissions(Permission::all());
        $superAdmin->syncRoles(['admin']);

        // ── Vendor demo account ──
        $vendorUser = User::firstOrCreate(
            ['email' => 'vendor@demo.com'],
            [
                'first_name' => 'Demo',
                'last_name'  => 'Vendor',
                'password'   => Hash::make('12345'),
                'status'     => 1,
            ]
        );
        $vendorUser->syncRoles(['vendor']);
        // Vendor gets permissions from their role (no direct permissions needed)
        $vendorUser->syncPermissions([]);

        // ── User demo account ──
        $normalUser = User::firstOrCreate(
            ['email' => 'user@demo.com'],
            [
                'first_name' => 'Demo',
                'last_name'  => 'User',
                'password'   => Hash::make('12345'),
                'status'     => 1,
            ]
        );
        $normalUser->syncRoles(['user']);
        $normalUser->syncPermissions([]);

        // ── Summary ──
        $this->command->info('✅ Roles and permissions seeded!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',  'superadmin@admin.com', '12345'],
                ['Vendor', 'vendor@demo.com',      '12345'],
                ['User',   'user@demo.com',         '12345'],
            ]
        );
    }
}