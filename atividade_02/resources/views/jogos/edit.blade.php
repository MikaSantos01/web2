<html>
<head></head>
<body>
    <h1>Editar Jogo</h1>

    <form action="{{ route('jogos.update', $jogo) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="{{ $jogo->nome }}" required>            
        </div>

        <div>
            <label for="genero">Gênero:</label>
            <select name="genero" id="genero">
                <option value="acao" @if($jogo->genero == 'acao') selected @endif>Ação</option>
                <option value="rpg" @if($jogo->genero == 'rpg') selected @endif>RPG</option>
                <option value="estrategia" @if($jogo->genero == 'estrategia') selected @endif>Estrategia</option>
            </select>          
        </div>

        <div>
            <label for="plataforma">Plataforma:</label>
            <input type="text" id="plataforma" name="plataforma" value="{{ $jogo->plataforma }}" required>            
        </div>

        <div>
            <label for="data">Data Lançamento:</label>
            <input type="date" id="data" name="data" value="{{ $jogo->data }}">            
        </div>

        <div>
            <label for="multiplayer">Multiplayer:</label>
            <input type="checkbox" id="multiplayer" name="multiplayer" @if($jogo->multiplayer) checked @endif>            
        </div>
        
        <button type="submit">
            Salvar
        </button>        
    </form>
</body>
</html>
