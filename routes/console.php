<?php

declare(strict_types=1);

use App\Actions\PurgeExpiredWorkspaceInvitations;
use Illuminate\Support\Facades\Schedule;

Schedule::call(fn () => resolve(PurgeExpiredWorkspaceInvitations::class)->handle())->daily();
