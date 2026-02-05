<?php

namespace Database\Factories;

use App\Models\Borrowing;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Borrowing>
 */
class BorrowingFactory extends Factory
{
    protected $model = Borrowing::class;

    public function definition(): array
    {
        $borrowedAt = $this->faker->dateTimeBetween('-60 days', 'now');
        
        // 70% de chance de já ter sido devolvido
        $returned = $this->faker->boolean(70);
        
        $returnedAt = null;
        if ($returned) {
            // Devolvido entre 1 e 30 dias após o empréstimo
            $returnedAt = $this->faker->dateTimeBetween(
                $borrowedAt,
                (clone $borrowedAt)->modify('+30 days')
            );
        }

        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'borrowed_at' => $borrowedAt,
            'returned_at' => $returnedAt,
        ];
    }

    /**
     * Empréstimo ainda não devolvido (ativo)
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'returned_at' => null,
        ]);
    }

    /**
     * Empréstimo com atraso
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'borrowed_at' => now()->subDays(20), // 20 dias atrás (5 dias de atraso)
            'returned_at' => null,
        ]);
    }
}