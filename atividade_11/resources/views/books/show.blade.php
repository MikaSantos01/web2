@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Livro</h1>

    {{-- MENSAGENS --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- AVISO DE MULTA --}}
    @if(auth()->check() && auth()->user()->temDebito())
        <div class="alert alert-danger">
            <i class="bi bi-cash-coin"></i>
            Você possui multa pendente no valor de 
            <strong>R$ {{ number_format(auth()->user()->debit, 2, ',', '.') }}</strong>.
            <br>
            Enquanto houver débito, não é possível realizar novos empréstimos.
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Título:</strong> {{ $book->title }}
        </div>

        <div class="card-body">

            {{-- EXIBIÇÃO DA CAPA DO LIVRO --}}
            @php
                $coverUrl = $book->cover_image ? asset('storage/' . $book->cover_image) : null;
            @endphp

            @if ($coverUrl)
                <div class="mb-4 text-center">
                    <img src="{{ $coverUrl }}"
                         alt="Capa do livro"
                         style="max-width: 220px; height: auto; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.2);">
                </div>
            @else
                <p><strong>Capa:</strong> Nenhuma capa cadastrada.</p>
            @endif

            <p><strong>Autor:</strong>
                <a href="{{ route('authors.show', $book->author->id) }}">
                    {{ $book->author->name }}
                </a>
            </p>

            <p><strong>Editora:</strong>
                <a href="{{ route('publishers.show', $book->publisher->id) }}">
                    {{ $book->publisher->name }}
                </a>
            </p>

            <p><strong>Categoria:</strong>
                <a href="{{ route('categories.show', $book->category->id) }}">
                    {{ $book->category->name }}
                </a>
            </p>
        </div>
    </div>

    {{-- FORMULÁRIO DE EMPRÉSTIMO --}}
    @can('borrow', $book)
        <div class="card mb-4 mt-4">
            <div class="card-header">
                @if(auth()->user()->isAdmin() || auth()->user()->isBibliotecario())
                    Registrar Empréstimo (Bibliotecário)
                @else
                    Emprestar este Livro
                @endif
            </div>
            <div class="card-body">
                
                {{-- SISTEMA BIBLIOTECÁRIO (Admin/Bibliotecário) --}}
                @if(auth()->user()->isAdmin() || auth()->user()->isBibliotecario())
                    <form action="{{ route('books.borrow', $book) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Selecione o Usuário</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="" selected>Selecione um usuário</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}" {{ $user->debit > 0 ? 'disabled' : '' }}>
                                        {{ $user->name }}
                                        @if($user->debit > 0)
                                            (MULTA: R$ {{ number_format($user->debit, 2, ',', '.') }})
                                        @endif
                                        @php
                                            $userBorrowings = $user->borrowings()->whereNull('returned_at')->count();
                                        @endphp
                                        ({{ $userBorrowings }}/5 livros)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">
                            Registrar Empréstimo
                        </button>
                    </form>

                {{-- SISTEMA DE AUTO-EMPRÉSTIMO (Usuários comuns) --}}
                @else
                    @php
                        $emprestimosAtivos = auth()->user()->borrowings()->whereNull('returned_at')->count();
                        $jaEmprestado = auth()->user()->borrowings()
                            ->where('book_id', $book->id)
                            ->whereNull('returned_at')
                            ->exists();
                    @endphp

                    <p>
                        <strong>Seus empréstimos ativos:</strong> 
                        <span class="badge {{ $emprestimosAtivos >= 5 ? 'bg-danger' : 'bg-info' }}">
                            {{ $emprestimosAtivos }}/5
                        </span>
                    </p>

                    @if($jaEmprestado)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Você já possui este livro emprestado.
                        </div>
                    @elseif(auth()->user()->temDebito())
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle"></i>
                            Você possui multa pendente e não pode realizar empréstimos.
                        </div>
                        <button class="btn btn-secondary" disabled>
                            Empréstimo bloqueado por multa
                        </button>
                    @elseif($emprestimosAtivos >= 5)
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle"></i>
                            Você atingiu o limite de 5 livros emprestados. 
                            Devolva algum livro antes de fazer um novo empréstimo.
                        </div>
                    @else
                        <form action="{{ route('books.borrow', $book) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-bookmark-plus"></i> Emprestar para Mim
                            </button>
                        </form>
                        <small class="text-muted d-block mt-2">
                            Você ainda pode emprestar {{ 5 - $emprestimosAtivos }} livro(s)
                        </small>
                    @endif
                @endif

            </div>
        </div>
    @endcan

    {{-- HISTÓRICO DE EMPRÉSTIMOS --}}
    <div class="card">
        <div class="card-header">Histórico de Empréstimos</div>
        <div class="card-body">
            @if($book->users->isEmpty())
                <p>Nenhum empréstimo registrado.</p>
            @else
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($book->users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($user->pivot->borrowed_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($user->pivot->returned_at)
                                    {{ \Carbon\Carbon::parse($user->pivot->returned_at)->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(is_null($user->pivot->returned_at))
                                    <span class="badge bg-warning">Em Aberto</span>
                                @else
                                    <span class="badge bg-success">Devolvido</span>
                                @endif

                                @if($user->debit > 0)
                                    <span class="badge bg-danger">Com Multa</span>
                                @endif
                            </td>
                            <td>
                                @if(is_null($user->pivot->returned_at))
                                    @can('update', \App\Models\Borrowing::find($user->pivot->id))
                                        <form action="{{ route('borrowings.return', $user->pivot->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="return confirm('Confirmar devolução deste livro?')">
                                                <i class="bi bi-arrow-return-left"></i> Devolver
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('books.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>

        @can('update', $book)
            <a href="{{ route('books.edit', $book) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
        @endcan

        @can('delete', $book)
            <form action="{{ route('books.destroy', $book) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                    <i class="bi bi-trash"></i> Excluir
                </button>
            </form>
        @endcan
    </div>
</div>
@endsection
