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
            // Users  (UserController)
            'users.list',           // GET  get-users
            'users.vendors',        // GET  get-vendors
            'users.create',         // POST add-user
            'users.update',         // POST update-user/{id}
            'users.delete',         // GET  delete-user/{id}

            // Products  (ProductController)
            'products.list',        // GET  get-product-backend
            'products.create',      // POST add-product-backend
            'products.colors',      // GET  get-colors-backend
            'products.categories',  // GET  get-categories-backend
            'products.update',      // POST update-product-backend/{id}
            'products.delete',      // GET  delete-product-backend/{id}

            // Categories  (ProductController)
            'categories.list',      // GET  get-categories-backend
            'categories.create',    // POST add-category-backend
            'categories.update',    // POST update-category-backend/{id}
            'categories.delete',    // GET  delete-category-backend/{id}

            // Stores  (StoreController)
            'stores.list',          // GET  get-stores-backend
            'stores.create',        // POST add-store
            'stores.update',        // POST update-store/{id}
            'stores.delete',        // DELETE stores resource
            'stores.category',      // GET  get-store-category-backend/{id}
            'stores.products',      // GET  stores/{storeId}/products

            // Roles & Permissions
            'roles.view',           // GET  /roles
            'roles.create',         // POST /roles
            'roles.update',         // PUT  /roles/{id}
            'roles.delete',         // DELETE /roles/{id}
            'permissions.view',     // GET  /permissions
 
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'api']);
        }

        // ── 2. Create roles & assign permissions ──

        // Admin — everything except user management (super admin does that)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $admin->syncPermissions([
            'products.list','products.create','users.list','users.vendors','users.create','users.update','users.delete',
            'stores.list','stores.create','stores.update','stores.delete',
            'categories.list','categories.create','categories.update','categories.delete',
            'roles.view','roles.create','roles.update','roles.delete',
            'permissions.view','products.update','products.delete','stores.products'
        ]);

        // Vendor — own products & store only
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'guard_name' => 'api']);
        $vendor->syncPermissions([
            'products.list','products.create','products.update','products.delete',
            'stores.list','stores.update',
            'stores.products'
        ]);

        // User — read only
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'api']);
        $user->syncPermissions([
            'products.list',
            'stores.list',
            'users.list',
            'stores.products'
            
        ]);

        // ── 3. Create default super admin (gets ALL permissions) ──
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@admin.com'],
            [
                'first_name' => 'Super',
                'last_name'  => 'Admin',
                'password'   => Hash::make('12345'),
                'status'     => 1,
                'role'      => 'admin',
            ]
        );
        // Give super admin every permission directly (bypasses role checks)
        $superAdmin->syncPermissions(Permission::all());
        // Also assign admin role so role checks work
        $superAdmin->syncRoles(['admin']);

        $this->command->info('✅ Roles, permissions, and super admin seeded!');
    }
}


// ── Register seeder in DatabaseSeeder.php ──
// database/seeders/DatabaseSeeder.php
//
// public function run(): void
// {
//     $this->call([
//         RolePermissionSeeder::class,
//     ]);
// }