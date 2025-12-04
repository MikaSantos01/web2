<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user)
    {
        return true; // Todos podem ver
    }

    public function view(User $user, Book $book)
    {
        return true; // Todos podem ver detalhes
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function update(User $user, Book $book)
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    public function delete(User $user, Book $book)
    {
        return $user->isAdmin(); // Apenas admin
    }
}
