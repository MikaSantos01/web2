<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $this->authorize('borrow', $book);

        // Valida o user_id
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // ✅ VALIDAÇÃO DE DÉBITO
        if ($user->temDebito()) {
            return redirect()
                ->back()
                ->with('error', 'Este usuário possui débito pendente de R$ ' . 
                    number_format($user->debit, 2, ',', '.') . 
                    ' e não pode realizar novos empréstimos.');
        }

        // Conta quantos livros o usuário tem emprestado
        $emprestimosAtivos = Borrowing::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->count();

        \Log::info("Usuário {$user->id} tem $emprestimosAtivos empréstimos ativos");

        // Verifica se o usuário já atingiu o limite de empréstimos
        if ($emprestimosAtivos >= 5) {
            return redirect()
                ->back()
                ->with('error', "Este usuário já atingiu o limite de 5 livros emprestados ($emprestimosAtivos/5). É necessário devolver algum livro antes de fazer um novo empréstimo.");
        }

        // Verifica se o usuário já tem este livro emprestado
        $jaEmprestado = Borrowing::where('user_id', $user->id)
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
            'user_id' => $user->id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        \Log::info("Empréstimo criado com sucesso para usuário {$user->id}, livro {$book->id}");

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

        $borrowings = Borrowing::where('user_id', $user->id)
            ->with('book')
            ->orderBy('borrowed_at', 'desc')
            ->paginate(10);

        $activeBorrowings = Borrowing::where('user_id', $user->id)
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

        // ✅ CALCULAR MULTA POR ATRASO
        $user = $borrowing->user;
        $dataDevolucao = now();
        $dataEmprestimo = Carbon::parse($borrowing->borrowed_at);
        $prazoEmDias = 15;
        $multaPorDia = 0.50;

        // Calcula data limite (empréstimo + 15 dias)
        $dataLimite = $dataEmprestimo->copy()->addDays($prazoEmDias);

        $multa = 0;
        $mensagem = 'Livro devolvido com sucesso!';

        // Se devolveu após o prazo, calcula multa
        if ($dataDevolucao->greaterThan($dataLimite)) {
            $diasAtraso = $dataLimite->diffInDays($dataDevolucao);
            $multa = $diasAtraso * $multaPorDia;

            // Adiciona multa ao débito do usuário
            $user->adicionarMulta($multa);

            $mensagem = "Livro devolvido com atraso de {$diasAtraso} dia(s). " .
                        "Multa de R$ " . number_format($multa, 2, ',', '.') . " aplicada.";

            \Log::info("Multa aplicada: Usuário {$user->id}, Valor: R$ {$multa}, Dias de atraso: {$diasAtraso}");
        }

        // Atualiza a devolução
        $borrowing->update([
            'returned_at' => $dataDevolucao,
        ]);

        \Log::info("Livro devolvido: Borrowing ID {$borrowing->id}");

        return redirect()
            ->back()
            ->with('success', $mensagem);
    }
}