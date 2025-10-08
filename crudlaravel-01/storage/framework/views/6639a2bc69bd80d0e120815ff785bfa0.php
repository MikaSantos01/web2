<html>
<head>
    <title>Cadastrar Book</title>
</head>
<body>

    <h1>Cadastrar Book</h1>

    <form action="<?php echo e(route('books.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div>
            <label for="title">Título:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div>
            <label for="author">Autor:</label>
            <input type="text" id="author" name="author" required>
        </div>

        <div>
            <label for="release_date">Data de Lançamento:</label>
            <input type="date" id="release_date" name="release_date" required>
        </div>

        <div>
            <label for="pages">Número de Páginas:</label>
            <input type="number" id="pages" name="pages" required>
        </div>

        <div>
            <label for="available">Disponível:</label>
            <select id="available" name="available">
                <option value="1">Sim</option>
                <option value="0">Não</option>
            </select>
        </div>

        <div>
            <label for="price">Preço:</label>
            <input type="number" step="0.01" id="price" name="price" required>
        </div>

        <button type="submit">Salvar</button>
    </form>

</body>
</html>
<?php /**PATH /home/mikai/web2/crudlaravel-01/resources/views/books/create.blade.php ENDPATH**/ ?>