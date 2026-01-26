<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;

class BorrowingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $this->authorize('borrow', $book);

        // Valida o user_id
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;

        // DEBUG: Conta quantos livros o usuário tem emprestado
        $emprestimosAtivos = Borrowing::where('user_id', $userId)
            ->whereNull('returned_at')
            ->count();

        \Log::info("Usuário $userId tem $emprestimosAtivos empréstimos ativos");

        // Verifica se o usuário já atingiu o limite de empréstimos
        if ($emprestimosAtivos >= 5) {
            return redirect()
                ->back()
                ->with('error', "Este usuário já atingiu o limite de 5 livros emprestados ($emprestimosAtivos/5). É necessário devolver algum livro antes de fazer um novo empréstimo.");
        }

        // Verifica se o usuário já tem este livro emprestado
        $jaEmprestado = Borrowing::where('user_id', $userId)
            ->where('book_id', $book->id)
            ->whereNull('returned_at')
            ->exists();

        if ($jaEmprestado) {
            return redirect()
                ->back()
                ->with('error', 'Este usuário já possui este livro emprestado.');
        }

        // Cria o empréstimo
        Borrowing::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        \Log::info("Empréstimo criado com sucesso para usuário $userId, livro {$book->id}");

        return redirect()
            ->route('books.show', $book)
            ->with('success', "Empréstimo registrado com sucesso! Este usuário tem " . ($emprestimosAtivos + 1) . "/5 livros emprestados.");
    }

    public function userBorrowings(User $user)
    {
        // Verifica se o usuário pode ver os empréstimos
        if (auth()->id() !== $user->id && !auth()->user()->isAdmin()) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        $borrowings = $user->borrowings()
            ->with('book')
            ->orderBy('borrowed_at', 'desc')
            ->paginate(10);

        $activeBorrowings = $user->borrowings()
            ->whereNull('returned_at')
            ->count();

        return view('borrowings.user', compact('user', 'borrowings', 'activeBorrowings'));
    }

    public function returnBook(Borrowing $borrowing)
    {
        $this->authorize('update', $borrowing);

        if ($borrowing->returned_at) {
            return redirect()
                ->back()
                ->with('error', 'Este livro já foi devolvido.');
        }

        $borrowing->update([
            'returned_at' => now(),
        ]);

        \Log::info("Livro devolvido: Borrowing ID {$borrowing->id}");

        return redirect()
            ->back()
            ->with('success', 'Livro devolvido com sucesso!');
    }
}