AdminPanel  Role-Based Management System
A full-stack role-based admin panel built with Vue.js 3 (frontend) and Laravel (backend). Three roles  Admin, Vendor, and User  each with their own access level, permissions, and dashboard view.

Project Overview
This is a sample role-based management system that demonstrates how to implement granular access control across a multi-role web application.
RoleDescriptionAdminFull access  manage users, roles, permissions, stores, products, categoriesVendorCan create/manage their own stores and products onlyUserRead-only access  can view data but cannot create, edit, or delete

Features
🔐 Authentication & Authorization

JWT-based login via Laravel Passport
Role & permission system via Spatie Laravel Permission
Dynamic sidebar navigation based on user permissions
Route-level guards on both frontend (Vue Router) and backend (middleware)
v-permission and v-role directives for template-level access control
Direct URL access blocked for unauthorized users

👥 User Management (Admin only)

View all users with role, status, and join date
Add new users with role assignment
Edit user details and change roles
Delete users
Profile picture upload

🏪 Store Management (Admin + Vendor)

Admin: full CRUD on all stores
Vendor: manage only their own stores
Assign categories and tags to stores
Upload store logo and cover image
View store-specific products

📦 Product Management (Admin + Vendor)

Add products with name, price, quantity, description
Assign products to stores and categories
Upload front image and multiple additional images
Color selection via multiselect
Low stock indicator (qty < 10)

🗂️ Category Management (Admin only)

Add, edit, delete categories
Upload category images
Live search filter

🛡️ Role & Permission Management (Admin only)

View all roles (Admin, Vendor, User) with permission breakdown
Toggle individual permissions per role
Assign or change a user's role in real-time
Permission counts and user counts per role

📊 Dashboard

Role-aware stat cards (each role sees relevant stats)
Recent users table (Admin)
Recent products table (Admin + Vendor)
Store overview list (Admin + Vendor)
Top categories with progress bars (all roles)
Role & permission overview grid (Admin only)
Export PDF button

🎨 UI/UX

Premium white sidebar with indigo accent (Mayors CRM-inspired)
PremiumLoader component (frosted glass + spinning gradient arc)
Fully responsive — mobile hamburger sidebar
Dynamic breadcrumbs, role badges, user avatars
Toast notifications for all actions
Confirmation modals for delete actions


Backend Setup (Laravel)
Requirements

PHP >= 8.1
Composer
MySQL >= 5.7.7 or MariaDB >= 10.3
Laravel >= 10

# ── Laravel ───────────────────────────────────────
composer install

php artisan serve                          # Start dev server
php artisan migrate                        # Run migrations
php artisan migrate:fresh --seed           # Fresh DB + seed
php artisan db:seed --class=RolePermissionSeeder  # Seed roles only
php artisan permission:cache-reset         # Clear permission cache
php artisan optimize:clear                 # Clear all cache
php artisan passport:install               # Install Passport keys
php artisan tinker                         # Interactive shell

# Check user roles in tinker
$user = \App\Models\User::find(1);
$user->getRoleNames();
$user->getAllPermissions()->pluck('name');


# Fix permission guard_name if needed (tinker)
\Spatie\Permission\Models\Role::query()->update(['guard_name' => 'api']);
\Spatie\Permission\Models\Permission::query()->update(['guard_name' => 'api']);
php artisan permission:cache-reset