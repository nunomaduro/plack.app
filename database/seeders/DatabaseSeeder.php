<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->create([
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'email' => 'enunomaduro@gmail.com',
            'email_verified_at' => now(),
        ]);

        $users = User::factory()->count(10)->create();

        Workspace::factory(10)->create([
            'user_id' => $admin->id,
        ])->each(function (Workspace $workspace) use ($users): void {

            Channel::factory()->create([
                'workspace_id' => $workspace->id,
            ])->each(function (Channel $channel) use ($users): void {

                foreach ($users as $user) {
                    Message::factory()->create([
                        'channel_id' => $channel->id,
                        'user_id' => $user->id,
                    ]);
                }

            });
        });
    }
}
