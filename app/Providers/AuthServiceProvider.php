<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('is-admin', function (User $user) {
            return $user->type === 'admin';
        });

        Gate::define('is-doctor', function (User $user) {
            return $user->type === 'doctor';
        });

        Gate::define('is-patient', function (User $user) {
            return $user->type === 'patient';
        });

        Gate::define('is-doctor-or-admin', function (User $user) {
            return in_array($user->type, ['doctor', 'admin']);
        });

        Gate::define('is-doctor-or-patient', function (User $user) {
            return in_array($user->type, ['doctor', 'patient']);
        });
    }
}
