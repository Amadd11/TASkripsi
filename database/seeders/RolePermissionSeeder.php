<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perawatRole = Role::firstOrCreate(['name' => 'perawat']);
        $pimpinanRole = Role::firstOrCreate(['name' => 'pimpinan']);

        $superAdmin = User::create([
            'name' => 'Super Admin',
            'nbm' => '10987',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'), // Jangan lupa untuk mengganti dengan password yang aman
        ]);

        // Membuat perawat
        $perawat = User::create([
            'name' => 'Perawat',
            'nbm' => '11109',
            'email' => 'perawat@example.com',
            'password' => Hash::make('password'),
        ]);

        // Menetapkan role 'perawat' ke pengguna ini
        $perawat->assignRole('perawat');

        // Membuat pimpinan
        $pimpinan = User::create([
            'name' => 'Pimpinan',
            'nbm' => '16789',
            'email' => 'pimpinan@example.com',
            'password' => Hash::make('password'),
        ]);

        // Menetapkan role 'pimpinan' ke pengguna ini
        $pimpinan->assignRole('pimpinan');
    }
}
