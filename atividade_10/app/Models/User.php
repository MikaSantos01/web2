<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // =====================
    // CONSTANTES DE PAPÉIS
    // =====================
    const ROLE_ADMIN = 'admin';
    const ROLE_BIBLIOTECARIO = 'bibliotecario';
    const ROLE_CLIENTE = 'cliente';

    // =====================
    // ATRIBUTOS DO MODEL
    // =====================
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =====================
    // RELACIONAMENTOS
    // =====================

    /**
     * Relacionamento com empréstimos (borrowings)
     * Um usuário tem muitos empréstimos
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Relacionamento many-to-many com livros através da tabela borrowings
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'borrowings')
                    ->withPivot('id', 'borrowed_at', 'returned_at')
                    ->withTimestamps();
    }

    // =====================
    // MÉTODOS DE VERIFICAÇÃO DE PAPEL
    // =====================

    /**
     * Verifica se o usuário é administrador
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Verifica se o usuário é bibliotecário
     */
    public function isBibliotecario()
    {
        return $this->role === self::ROLE_BIBLIOTECARIO;
    }

    /**
     * Verifica se o usuário é cliente
     */
    public function isCliente()
    {
        return $this->role === self::ROLE_CLIENTE;
    }

    // =====================
    // MÉTODOS AUXILIARES PARA EMPRÉSTIMOS
    // =====================

    /**
     * Retorna a quantidade de empréstimos ativos do usuário
     */
    public function emprestimosAtivos()
    {
        return $this->borrowings()->whereNull('returned_at')->count();
    }

    /**
     * Verifica se o usuário pode emprestar mais livros (limite de 5)
     */
    public function podeEmprestar()
    {
        return $this->emprestimosAtivos() < 5;
    }

    /**
     * Verifica se o usuário já tem um livro específico emprestado
     */
    public function temLivroEmprestado($bookId)
    {
        return $this->borrowings()
            ->where('book_id', $bookId)
            ->whereNull('returned_at')
            ->exists();
    }
}