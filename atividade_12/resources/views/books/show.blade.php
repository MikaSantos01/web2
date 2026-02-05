@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <h1 class="my-4">Detalhes do Livro</h1>

    <div class="card">
        <div class="card-header">
            <strong>Título:</strong> {{ $book->title }}
        </div>

        <div class="card-body">

           {{-- EXIBIÇÃO DA CAPA DO LIVRO — usando APENAS o campo "cover" --}}
    @php
    use Illuminate\Support\Facades\Storage;

    $coverUrl = $book->cover ? Storage::url($book->cover) : null;
    @endphp

    @if ($coverUrl)
    <div class="mb-4 text-center">
        <img src="{{ $coverUrl }}"
             alt="Capa do livro - {{ $book->title }}"
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

    <!-- Formulário para Empréstimos -->
    <div class="card mb-4 mt-4">
        <div class="card-header">Registrar Empréstimo</div>
        <div class="card-body">
            <form action="{{ route('books.borrow', $book) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="user_id" class="form-label">Usuário</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="" selected>Selecione um usuário</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Registrar Empréstimo</button>
            </form>
        </div>
    </div>

    <!-- Histórico de Empréstimos -->
<div class="card">
    <div class="card-header">Histórico de Empréstimos</div>
    <div class="card-body">
        @if($borrowings->isEmpty())
            <p>Nenhum empréstimo registrado.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Data de Empréstimo</th>
                        <th>Data de Devolução</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($borrowings as $borrowing)
                    <tr>
                        <td>
                            <a href="{{ route('users.show', $borrowing->user->id) }}">
                                {{ $borrowing->user->name }}
                            </a>
                        </td>
                        <td>{{ optional($borrowing->borrowed_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($borrowing->returned_at)
                                {{ $borrowing->returned_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-danger">Em Aberto</span>
                            @endif
                        </td>
                        <td>
                            @if(is_null($borrowing->returned_at) && (auth()->user()->isAdmin() || auth()->user()->isBibliotecario()))
                                <form action="{{ route('borrowings.return', $borrowing) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-warning btn-sm" onclick="return confirm('Confirmar devolução?')">Devolver</button>
                                </form>
                            @else
                                {{-- nada --}}
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>


    <a href="{{ route('books.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>
@endsection