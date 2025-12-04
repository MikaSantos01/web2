<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Author $author)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function update(User $user, Author $author)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function delete(User $user, Author $author)
    {
        return $user->isAdmin();
    }
}
