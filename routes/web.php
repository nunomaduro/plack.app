<?php

declare(strict_types=1);

use App\Http\Controllers\AcceptWorkspaceInvitationController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChannelTypingController;
use App\Http\Controllers\DeclineWorkspaceInvitationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RegenerateWorkspaceJoinLinkController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEmailResetNotificationController;
use App\Http\Controllers\UserEmailVerificationController;
use App\Http\Controllers\UserEmailVerificationNotificationController;
use App\Http\Controllers\UserPasswordController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserTwoFactorAuthenticationController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceInvitationController;
use App\Http\Controllers\WorkspaceJoinController;
use App\Http\Controllers\WorkspaceMemberController;
use App\Http\Controllers\WorkspaceSettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('welcome'))->name('home');

Route::get('workspaces/join/{joinCode}', [WorkspaceJoinController::class, 'show'])
    ->name('workspace.join');

Route::middleware(['auth', 'verified'])->group(function (): void {
    // Workspaces...
    Route::get('workspaces', [WorkspaceController::class, 'index'])->name('workspace.index');

    Route::post('workspaces', [WorkspaceController::class, 'store'])
        ->name('workspace.store');

    // Members & owners may enter a workspace and browse its channels...
    Route::middleware(['workspace.member'])->group(function (): void {
        Route::get('workspaces/{workspace}', [WorkspaceController::class, 'show'])
            ->name('workspace.show');

        Route::scopeBindings()->group(function (): void {
            Route::get('workspaces/{workspace}/channels/{channel}', [ChannelController::class, 'show'])
                ->name('channel.show');

            Route::post('workspaces/{workspace}/channels/{channel}/messages', [MessageController::class, 'store'])
                ->name('messages.store');

            Route::post('workspaces/{workspace}/channels/{channel}/typing', ChannelTypingController::class)
                ->middleware('throttle:60,1')
                ->name('channel.typing');
        });
    });

    // Only owners may manage a workspace, its channels, members and invitations...
    Route::middleware(['workspace.owner'])->group(function (): void {
        Route::get('workspaces/{workspace}/settings', WorkspaceSettingsController::class)
            ->name('workspace.settings');

        Route::patch('workspaces/{workspace}', [WorkspaceController::class, 'update'])
            ->name('workspace.update');

        Route::delete('workspaces/{workspace}', [WorkspaceController::class, 'destroy'])
            ->name('workspace.destroy');

        Route::post('workspaces/{workspace}/join-link', RegenerateWorkspaceJoinLinkController::class)
            ->name('workspace.join-link.regenerate');

        // Invitations & Members...
        Route::post('workspaces/{workspace}/invitations', [WorkspaceInvitationController::class, 'store'])
            ->name('workspace.invitations.store');

        Route::delete('workspaces/{workspace}/invitations/{invitation}', [WorkspaceInvitationController::class, 'destroy'])
            ->name('workspace.invitations.destroy');

        Route::delete('workspaces/{workspace}/members/{user}', [WorkspaceMemberController::class, 'destroy'])
            ->name('workspace.members.destroy');

        // Channels...
        Route::post('workspaces/{workspace}/channels', [ChannelController::class, 'store'])
            ->name('channel.store');

        Route::scopeBindings()->group(function (): void {
            Route::patch('workspaces/{workspace}/channels/{channel}', [ChannelController::class, 'update'])
                ->name('channel.update');

            Route::delete('workspaces/{workspace}/channels/{channel}', [ChannelController::class, 'destroy'])
                ->name('channel.destroy');
        });
    });

    // Workspace Invitations...
    Route::post('invitations/{invitation}/accept', AcceptWorkspaceInvitationController::class)
        ->name('invitations.accept');

    Route::delete('invitations/{invitation}', DeclineWorkspaceInvitationController::class)
        ->name('invitations.decline');

    // Public Workspace Joins...
    Route::post('workspace-joins/{joinCode}/accept', [WorkspaceJoinController::class, 'store'])
        ->name('workspace-joins.accept');

    Route::delete('workspace-joins/{joinCode}', [WorkspaceJoinController::class, 'destroy'])
        ->name('workspace-joins.decline');
});

Route::middleware('auth')->group(function (): void {
    // User...
    Route::delete('user', [UserController::class, 'destroy'])->name('user.destroy');

    // User Profile...
    Route::redirect('settings', '/settings/profile');
    Route::get('settings/profile', [UserProfileController::class, 'edit'])->name('user-profile.edit');
    Route::patch('settings/profile', [UserProfileController::class, 'update'])->name('user-profile.update');

    // User Password...
    Route::get('settings/password', [UserPasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [UserPasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('password.update');

    // Appearance...
    Route::get('settings/appearance', fn () => Inertia::render('appearance/update'))->name('appearance.edit');

    // User Two-Factor Authentication...
    Route::get('settings/two-factor', [UserTwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');
});

Route::middleware('guest')->group(function (): void {
    // User...
    Route::get('register', [UserController::class, 'create'])
        ->name('register');
    Route::post('register', [UserController::class, 'store'])
        ->name('register.store');

    // User Password...
    Route::get('reset-password/{token}', [UserPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [UserPasswordController::class, 'store'])
        ->name('password.store');

    // User Email Reset Notification...
    Route::get('forgot-password', [UserEmailResetNotificationController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [UserEmailResetNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');

    // Session...
    Route::get('login', [SessionController::class, 'create'])
        ->name('login');
    Route::post('login', [SessionController::class, 'store'])
        ->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    // User Email Verification...
    Route::get('verify-email', [UserEmailVerificationNotificationController::class, 'create'])
        ->name('verification.notice');
    Route::post('email/verification-notification', [UserEmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // User Email Verification...
    Route::get('verify-email/{id}/{hash}', [UserEmailVerificationController::class, 'update'])
        ->middleware(['bento.signature', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Session...
    Route::post('logout', [SessionController::class, 'destroy'])
        ->name('logout');
});
