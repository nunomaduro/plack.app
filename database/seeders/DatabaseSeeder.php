<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'email' => 'enunomaduro@gmail.com',
            'email_verified_at' => now(),
        ]);
    }
}
