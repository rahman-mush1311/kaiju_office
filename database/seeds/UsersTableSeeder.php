<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User([
            'name' => 'System Admin',
            'email' => 'systemadmin@deligram.com',
            'password' => Hash::make('123456'),
            'roles' => [Role::ADMIN]
        ]);

        $user->save();
    }
}
