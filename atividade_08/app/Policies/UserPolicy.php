<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        // Admin e bibliotecário podem ver a lista
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function view(User $user, User $model)
    {
        // Admin vê todos, bibliotecário vê todos, usuário vê só o próprio perfil
        return $user->isAdmin() || $user->isBibliotecario() || $user->id === $model->id;
    }

    public function update(User $user, User $model)
    {
        // Só admin pode editar todos, usuário só pode editar o próprio perfil
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        // Só admin pode deletar
        return $user->isAdmin();
    }
}
