<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Isolatable;

final class ClearExpiredStatuses extends Command implements Isolatable
{
    protected $signature = 'status:clear-expired';

    protected $description = 'Clear expired user statuses';

    public function handle(): void
    {
        $cleared = User::query()
            ->whereNotNull('status_expires_at')
            ->where('status_expires_at', '<=', now())
            ->update([
                'status_emoji' => null,
                'status_text' => null,
                'status_expires_at' => null,
            ]);

        $this->info("Cleared {$cleared} expired status(es).");
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->command(self::class)->everyMinute();
    }
}
