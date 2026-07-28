<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Gate for System Admin
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate for Finance
        Gate::define('is-finance', function (User $user) {
            return $user->role === 'finance';
        });

        // Gate for Procurement
        Gate::define('is-procurement', function (User $user) {
            return in_array($user->role, ['procurement', 'admin']);
        });

        // Gate for General Manager
        Gate::define('is-manager', function (User $user) {
            return $user->role === 'manager';
        });

        // Gate for a standard Employee
        Gate::define('is-employee', function (User $user) {
            return $user->role === 'employee';
        });

        // Gate to check if user is any "approver" type (Admin, Finance, etc.)
        Gate::define('is-approver', function (User $user) {
            return in_array($user->role, ['admin', 'finance', 'procurement', 'manager']);
        });

        // Gate to check if user can manage budgets
        Gate::define('can-manage-budgets', function (User $user) {
            // Only Admin, Manager, and Finance can set budgets
            return in_array($user->role, ['admin', 'manager', 'finance']);
        });

        // Gate to check if user can manage vendors
        Gate::define('can-manage-vendors', function (User $user) {
            return in_array($user->role, ['procurement', 'finance', 'admin']);
        });
    }
}
