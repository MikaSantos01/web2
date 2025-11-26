<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Todos podem ver a lista de livros.
     */
    public function viewAny(User $user): bool
    {
        return true; // admin, bibliotecario e cliente podem ver
    }

    /**
     * Todos podem visualizar um livro.
     */
    public function view(User $user, Book $book): bool
    {
        return true; // admin, bibliotecario e cliente podem ver
    }

    /**
     * Apenas admin e bibliotecario podem criar livros.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Apenas admin e bibliotecario podem atualizar livros.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Apenas admin e bibliotecario podem deletar livros.
     */
    public function delete(User $user, Book $book): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Apenas admin pode restaurar livros.
     */
    public function restore(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    /**
     * Apenas admin pode deletar permanentemente.
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }
}
