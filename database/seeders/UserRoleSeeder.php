<?php

namespace Database\Seeders;

use App\Enums\UserRoleStatus;
use App\Models\User;
use App\Models\UserRole;
use App\Providers\AuthServiceProvider;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserRole::factory()->count(1)->create([
            'name' => 'Super Admin',
            'created_by' => User::find(AuthServiceProvider::SUPER_ADMIN),
            'status' => UserRoleStatus::ACTIVE,
        ]);
    }
}
