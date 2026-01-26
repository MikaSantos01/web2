<?php

namespace App\Policies;

use App\Models\Publisher;
use App\Models\User;

class PublisherPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Publisher $publisher)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function update(User $user, Publisher $publisher)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function delete(User $user, Publisher $publisher)
    {
        return $user->isAdmin();
    }
}
