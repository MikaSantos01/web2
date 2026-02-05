<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BorrowingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        // Usuário pode ver seu próprio empréstimo ou admin pode ver todos
        return $user->id === $borrowing->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model (devolver livro).
     */
    public function update(User $user, Borrowing $borrowing): bool
    {
        // Usuário pode devolver seu próprio livro ou admin pode devolver qualquer livro
        return $user->id === $borrowing->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Borrowing $borrowing): bool
    {
        // Apenas admin pode deletar registros de empréstimo
        return $user->isAdmin();
    }
}