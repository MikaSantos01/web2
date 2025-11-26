<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Book;
use App\Policies\BookPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Book::class => BookPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('manage-users', function ($user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('manage-categories', function ($user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('manage-books', function ($user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('manage-authors', function ($user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('manage-publishers', function ($user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });

        Gate::define('borrow-books', function ($user) {
            return true;
        });

        Gate::define('return-books', function ($user) {
            return $user->isAdmin() || $user->isBibliotecario();
        });
    }
}