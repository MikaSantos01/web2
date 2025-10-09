<html>
<head></head>
<body>

    <h1>Cadastrar Jogos</h1>

    <form action="{{ route('jogos.store') }}" method="POST">
        @csrf
        <div>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>            
        </div>

        <div>
            <label for="genero">Gênero: </label>
            <select name="genero" id="genero">
                <option value="acao">Ação</option>
                <option value="rpg">RPG</option>
                <option value="estrategia">Estratégia</option>
            </select>          
        </div>

        <div>
            <label for="plataforma">Plataforma: </label>
            <input type="text" id="plataforma" name="plataforma" required>            
        </div>

        <div>
            <label for="data">Data Lançamento: </label>
            <input type="date" id="data" name="data">            
        </div>

        <div>
            <label for="multiplayer">Multiplayer: </label>
            <input type="checkbox" id="multiplayer" name="multiplayer">            
        </div>
        
        <button type="submit">
            Salvar
        </button>        
    </form>
</body>
</html>
