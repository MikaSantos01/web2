<html>
<head></head>
<body>    
    <h1>Lista dos Jogos</h1>     
<a href="{{ route('jogos.create') }}">Adicionar</a>

    @if($jogos->isEmpty())
        <p>Nenhum jogo cadastrado.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Gênero</th>
                <th>Plataforma</th>
                <th>Data Lançamento</th>
                <th>Multiplayer</th>
                <th>Ações</th>
            </tr>
            @foreach($jogos as $jogo)
            <tr>
                <td>{{ $jogo->id }}</td>
                <td>{{ $jogo->nome }}</td>
                <td>{{ $jogo->genero }}</td>
                <td>{{ $jogo->plataforma }}</td>
                <td>{{ $jogo->data ?? '-' }}</td>
                <td> {{ $jogo->multiplayer }}</td>
                <td>
                    <a href="{{ route('jogos.show', $jogo) }}">Visualizar</a>
                    <a href="{{ route('jogos.edit', $jogo) }}">Editar</a>

                    <form action="{{ route('jogos.destroy', $jogo) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Deseja excluir este jogo?')">Excluir</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    @endif

</body>
</html>
