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
        'debit',
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
     * Um usuário tem muitos empréstimos
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Muitos livros através da tabela borrowings
     */
    public function books()
    {
        return $this->belongsToMany(Book::class, 'borrowings')
            ->withPivot('id', 'borrowed_at', 'returned_at')
            ->withTimestamps();
    }

    // =====================
    // MÉTODOS DE PAPEL
    // =====================

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isBibliotecario()
    {
        return $this->role === self::ROLE_BIBLIOTECARIO;
    }

    public function isCliente()
    {
        return $this->role === self::ROLE_CLIENTE;
    }

    // =====================
    // MÉTODOS DE EMPRÉSTIMO
    // =====================

    /**
     * Quantidade de empréstimos ativos
     */
    public function emprestimosAtivos()
    {
        return $this->borrowings()
            ->whereNull('returned_at')
            ->count();
    }

    /**
     * Verifica se o usuário possui débito
     */
    public function temDebito()
    {
        return $this->debit > 0;
    }

    /**
     * Verifica se o usuário pode emprestar livros
     * - máximo de 5
     * - não pode ter débito
     */
    public function podeEmprestar()
    {
        return $this->emprestimosAtivos() < 5 && !$this->temDebito();
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

    // =====================
    // MÉTODOS DE MULTA
    // =====================

    /**
     * Adiciona valor de multa ao débito do usuário
     */
    public function adicionarMulta(float $valor)
    {
        $this->debit += $valor;
        $this->save();
    }

    /**
     * Quita todas as multas do usuário
     * (ação do bibliotecário)
     */
    public function quitarDebito()
    {
        $this->debit = 0;
        $this->save();
    }
}
