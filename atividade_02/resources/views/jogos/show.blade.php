<html>
<head></head>
<body>    
    <h1>Detalhes do Jogo</h1>        

    <div>
        <p><strong>ID:</strong> {{ $jogo->id }}</p>
        <p><strong>Nome:</strong> {{ $jogo->nome }}</p>
        <p><strong>Gênero:</strong> {{ $jogo->genero }}</p>
        <p><strong>Plataforma:</strong> {{ $jogo->plataforma }}</p>
        <p><strong>Data de Lançamento:</strong> {{ $jogo->data ?? '-' }}</p>
        <p><strong>Multiplayer:</strong> {{ $jogo->multiplayer }}</p>
    </div>

    <div>
        <a href="{{ route('jogos.index') }}">Voltar para a lista</a>
        <a href="{{ route('jogos.edit', $jogo) }}">Editar</a>
    </div>

</body>
</html>
