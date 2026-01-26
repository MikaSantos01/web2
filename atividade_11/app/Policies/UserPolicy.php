<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function view(User $user, User $model)
    {
        return $user->isAdmin() ||
               $user->isBibliotecario() ||
               $user->id === $model->id; // Cliente só vê o próprio perfil
    }

    public function update(User $user, User $model)
    {
        return $user->isAdmin() ||
               $user->id === $model->id; // Cliente só edita o próprio
    }

    public function delete(User $user, User $model)
    {
        return $user->isAdmin();
    }
}
