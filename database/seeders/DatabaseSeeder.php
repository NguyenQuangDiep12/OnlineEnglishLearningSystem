<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // FIX: was 'name' => 'fullname' (wrong key, wrong value)
        User::factory()->create([
            'fullname'      => 'Test User',
            'email'         => 'test@example.com',
            'password_hash' => Hash::make('123456'),
            'role'          => 'admin',
        ]);

        // Thêm tài khoản mẫu cho từng role
        User::factory()->create([
            'fullname'      => 'Instructor Demo',
            'email'         => 'instructor@example.com',
            'password_hash' => Hash::make('123456'),
            'role'          => 'instructor',
        ]);

        User::factory()->create([
            'fullname'      => 'Student Demo',
            'email'         => 'student@example.com',
            'password_hash' => Hash::make('123456'),
            'role'          => 'student',
        ]);
    }
}

?>