<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'returned_at',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    /**
     * Relacionamento com User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com Book
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Verifica se está em atraso
     */
    public function estaAtrasado(): bool
    {
        if ($this->returned_at) {
            return false; // Já foi devolvido
        }

        $dataLimite = $this->borrowed_at->copy()->addDays(15);
        return now()->greaterThan($dataLimite);
    }

    /**
     * Calcula dias de atraso
     */
    public function diasDeAtraso(): int
    {
        if (!$this->estaAtrasado()) {
            return 0;
        }

        $dataLimite = $this->borrowed_at->copy()->addDays(15);
        return $dataLimite->diffInDays(now());
    }

    /**
     * Calcula multa pendente
     */
    public function multaPendente(): float
    {
        return $this->diasDeAtraso() * 0.50;
    }
}