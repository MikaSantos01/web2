<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Borrowing;
use App\Models\Book;

class UserBorrowingSeeder extends Seeder
{
    public function run(): void
    {
        // Pega todos os livros disponíveis
        $books = Book::all();

        if ($books->isEmpty()) {
            $this->command->warn('Nenhum livro encontrado. Execute primeiro o AuthorPublisherBookSeeder.');
            return;
        }

        // Criar 10 usuários
        User::factory(10)->create()->each(function ($user) use ($books) {
            
            // Cada usuário faz entre 1 e 5 empréstimos
            $numEmprestimos = rand(1, 5);
            
            // Seleciona livros aleatórios (sem repetição)
            $livrosEscolhidos = $books->random(min($numEmprestimos, $books->count()));
            
            foreach ($livrosEscolhidos as $book) {
                // Decide o status do empréstimo
                $rand = rand(1, 100);
                
                if ($rand <= 60) {
                    // 60% - Devolvido normalmente (sem atraso)
                    $borrowedAt = now()->subDays(rand(20, 50));
                    $returnedAt = (clone $borrowedAt)->addDays(rand(5, 14)); // Devolveu antes do prazo
                    
                    Borrowing::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'borrowed_at' => $borrowedAt,
                        'returned_at' => $returnedAt,
                    ]);
                    
                } elseif ($rand <= 85) {
                    // 25% - Ativo (emprestado recentemente, ainda no prazo)
                    Borrowing::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'borrowed_at' => now()->subDays(rand(1, 10)),
                        'returned_at' => null,
                    ]);
                    
                } elseif ($rand <= 95) {
                    // 10% - Atrasado (não devolveu ainda)
                    $borrowing = Borrowing::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'borrowed_at' => now()->subDays(rand(20, 30)), // 5 a 15 dias de atraso
                        'returned_at' => null,
                    ]);
                    
                    // Calcula e aplica multa
                    $diasAtraso = $borrowing->diasDeAtraso();
                    if ($diasAtraso > 0) {
                        $user->adicionarMulta($diasAtraso * 0.50);
                    }
                    
                } else {
                    // 5% - Devolvido com atraso (multa já aplicada)
                    $borrowedAt = now()->subDays(rand(30, 60));
                    $returnedAt = (clone $borrowedAt)->addDays(rand(18, 25)); // Devolveu 3 a 10 dias atrasado
                    
                    $borrowing = Borrowing::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'borrowed_at' => $borrowedAt,
                        'returned_at' => $returnedAt,
                    ]);
                    
                    // Calcula multa do período que ficou em atraso
                    $dataLimite = $borrowedAt->copy()->addDays(15);
                    $diasAtraso = $dataLimite->diffInDays($returnedAt);
                    
                    if ($diasAtraso > 0) {
                        $user->adicionarMulta($diasAtraso * 0.50);
                    }
                }
            }
        });

        $this->command->info('Usuários e empréstimos criados com sucesso!');
        $this->command->info('Total de empréstimos: ' . Borrowing::count());
        $this->command->info('Usuários com débito: ' . User::where('debit', '>', 0)->count());
    }
}