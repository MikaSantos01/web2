<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        // Permite admin E bibliotecário ver a lista
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function view(User $user, User $model)
    {
        // Admin vê todos, usuários veem apenas seu próprio perfil
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function update(User $user, User $model)
    {
        // Admin edita todos, usuários editam apenas seu próprio perfil
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        // Só admin deleta
        return $user->isAdmin();
    }
}