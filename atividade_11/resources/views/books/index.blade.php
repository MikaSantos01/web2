@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Lista de Livros</h1>

    {{-- AVISO DE MULTA --}}
    @if(auth()->check() && auth()->user()->temDebito())
        <div class="alert alert-danger">
            <i class="bi bi-cash-coin"></i>
            Você possui multa pendente de
            <strong>R$ {{ number_format(auth()->user()->debit, 2, ',', '.') }}</strong>.
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
                <tr>
                    <td>{{ $book->id }}</td>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->author->name }}</td>
                    <td>
                        <a href="{{ route('books.show', $book->id) }}" class="btn btn-info btn-sm">
                            Visualizar
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $books->links() }}
</div>
@endsection
