<?php

namespace Database\Seeders;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Company;
use App\Models\CompanyCategory;
use App\Models\CompanyOwner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'company-owner']);

        $categories = [
            'Technology & Software',
            'Marketing & Creative',
            'Construction & Real Estate',
            'Education & Training',
            'Finance & Accounting',
            'Healthcare',
            'Retail & E-commerce',
            'Other'
        ];

        foreach ($categories as $category) {
            CompanyCategory::firstOrCreate([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }

        $admin = User::firstOrCreate(
            ['email'             => 'admin@localhost.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]
        );
        
        $admin->syncRoles(['admin']);
    }
}