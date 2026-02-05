@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Lista de Livros</h1>

    {{-- Botões de adicionar livro (apenas para quem pode criar) --}}
    <div class="mb-3">
        @can('create', App\Models\Book::class)
            <a href="{{ route('books.create.id') }}" class="btn btn-success">Adicionar Livro (ID)</a>
            <a href="{{ route('books.create.select') }}" class="btn btn-success">Adicionar Livro (Select)</a>
        @endcan
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Título</th>
                <th>Autor</th>
                <th>Editora</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>{{ $book->title }}</td>
                <td>{{ $book->author->name }}</td>
                <td>{{ $book->publisher->name }}</td>
                <td>
                    {{-- Sempre mostrar Visualizar --}}
                    <a href="{{ url('/api/books/'.$book->id) }}" target="_blank"class="btn btn-info">Visualizar</a>

                    {{-- Editar --}}
                    @can('update', $book)
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">Editar</a>
                    @endcan

                    {{-- Excluir --}}
                    @can('delete', $book)
                        <form action="{{ route('books.destroy', $book) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </form>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-3">
    {{ $books->links('pagination::bootstrap-5') }}
</div>

@endsection