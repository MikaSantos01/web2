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
            <i class="bi bi-exclamation-triangle-fill"></i>
            Você possui multa pendente no valor de
            <strong>R$ {{ number_format(auth()->user()->debit, 2, ',', '.') }}</strong>.
            <br>
            Enquanto houver débito, não é possível realizar novos empréstimos.
            <br>
            <small>Procure o bibliotecário para efetuar o pagamento.</small>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Título:</strong> {{ $book->title }}
        </div>

        <div class="card-body">
            @php
                $coverUrl = $book->cover_image ? asset('storage/' . $book->cover_image) : null;
            @endphp

            @if ($coverUrl)
                <div class="mb-4 text-center">
                    <img src="{{ $coverUrl }}" alt="{{ $book->title }}" class="img-fluid" style="max-width: 220px;">
                </div>
            @endif

            <p><strong>Autor:</strong> {{ $book->author->name }}</p>
            <p><strong>Editora:</strong> {{ $book->publisher->name }}</p>
            <p><strong>Categoria:</strong> {{ $book->category->name }}</p>
        </div>
    </div>

    {{-- FORMULÁRIO DE EMPRÉSTIMO --}}
    @can('borrow', $book)
    <div class="card mt-4">
        <div class="card-header">
            <strong>Realizar Empréstimo</strong>
        </div>
        <div class="card-body">

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
                    <i class="bi bi-info-circle"></i>
                    Você já possui este livro emprestado.
                </div>

            @elseif(auth()->user()->temDebito())
                <div class="alert alert-danger">
                    <i class="bi bi-cash-coin"></i>
                    Você possui multa pendente e não pode realizar empréstimos.
                </div>
                <button class="btn btn-secondary" disabled>
                    <i class="bi bi-lock-fill"></i> Empréstimo bloqueado
                </button>

            @elseif($emprestimosAtivos >= 5)
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Limite de 5 empréstimos simultâneos atingido.
                </div>
                <button class="btn btn-secondary" disabled>
                    <i class="bi bi-x-circle"></i> Limite atingido
                </button>

            @else
                <form action="{{ route('books.borrow', $book) }}" method="POST">
                    @csrf
                    <button class="btn btn-success">
                        <i class="bi bi-book"></i> Emprestar este livro
                    </button>
                </form>
            @endif

        </div>
    </div>
    @endcan

    {{-- MEUS EMPRÉSTIMOS DESTE LIVRO --}}
    @auth
    @php
        $meusEmprestimos = auth()->user()->borrowings()
            ->where('book_id', $book->id)
            ->orderBy('borrowed_at', 'desc')
            ->get();
    @endphp

    @if($meusEmprestimos->isNotEmpty())
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <strong>Meus Empréstimos deste Livro</strong>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Data Empréstimo</th>
                        <th>Data Devolução</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($meusEmprestimos as $emprestimo)
                    <tr>
                        <td>{{ $emprestimo->borrowed_at->format('d/m/Y') }}</td>
                        <td>
                            @if($emprestimo->returned_at)
                                {{ $emprestimo->returned_at->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Em aberto</span>
                            @endif
                        </td>
                        <td>
                            @if(is_null($emprestimo->returned_at))
                                @php
                                    $dataLimite = $emprestimo->borrowed_at->copy()->addDays(15);
                                    $atrasado = now()->greaterThan($dataLimite);
                                    $diasAtraso = $atrasado ? $dataLimite->diffInDays(now()) : 0;
                                @endphp

                                @if($atrasado)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Atrasado ({{ $diasAtraso }} dia{{ $diasAtraso > 1 ? 's' : '' }})
                                    </span>
                                    <br>
                                    <small class="text-danger">
                                        Multa: R$ {{ number_format($diasAtraso * 0.50, 2, ',', '.') }}
                                    </small>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock"></i> Em dia
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        Prazo: {{ $dataLimite->format('d/m/Y') }}
                                    </small>
                                @endif
                            @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Devolvido
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(is_null($emprestimo->returned_at))
                                <form action="{{ route('borrowings.return', $emprestimo) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Confirma a devolução deste livro?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-primary">
                                        <i class="bi bi-arrow-return-left"></i> Devolver
                                    </button>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endauth

    {{-- HISTÓRICO COMPLETO DE EMPRÉSTIMOS --}}
    @can('viewAny', App\Models\Borrowing::class)
    <div class="card mt-4">
        <div class="card-header">
            <strong>Histórico Completo de Empréstimos</strong>
        </div>
        <div class="card-body">
            @if($book->borrowings->isEmpty())
                <p class="text-muted">Nenhum empréstimo registrado para este livro.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Data Empréstimo</th>
                            <th>Prazo</th>
                            <th>Data Devolução</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($book->borrowings()->with('user')->orderBy('borrowed_at', 'desc')->get() as $borrowing)
                        <tr>
                            <td>{{ $borrowing->user->name }}</td>
                            <td>{{ $borrowing->borrowed_at->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $prazo = $borrowing->borrowed_at->copy()->addDays(15);
                                @endphp
                                {{ $prazo->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($borrowing->returned_at)
                                    {{ $borrowing->returned_at->format('d/m/Y') }}
                                    
                                    @php
                                        $prazo = $borrowing->borrowed_at->copy()->addDays(15);
                                        $devolucaoAtrasada = $borrowing->returned_at->greaterThan($prazo);
                                    @endphp
                                    
                                    @if($devolucaoAtrasada)
                                        <br>
                                        <small class="text-danger">
                                            ({{ $prazo->diffInDays($borrowing->returned_at) }} dia(s) de atraso)
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(is_null($borrowing->returned_at))
                                    @php
                                        $dataLimite = $borrowing->borrowed_at->copy()->addDays(15);
                                        $atrasado = now()->greaterThan($dataLimite);
                                        $diasAtraso = $atrasado ? $dataLimite->diffInDays(now()) : 0;
                                    @endphp

                                    @if($atrasado)
                                        <span class="badge bg-danger">
                                            Atrasado ({{ $diasAtraso }} dia{{ $diasAtraso > 1 ? 's' : '' }})
                                        </span>
                                    @else
                                        <span class="badge bg-warning">Em Aberto</span>
                                    @endif
                                @else
                                    <span class="badge bg-success">Devolvido</span>
                                @endif

                                @if($borrowing->user->debit > 0)
                                    <br>
                                    <span class="badge bg-danger mt-1">
                                        Débito: R$ {{ number_format($borrowing->user->debit, 2, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    @endcan

</div>
@endsection