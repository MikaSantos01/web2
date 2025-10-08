<html>
<head></head>
<body>
    <h1>Editar Livro</h1>

    <form action="<?php echo e(route('books.update', $book)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div>
            <label for="title">Título:</label>
            <input type="text" id="title" name="title" value="<?php echo e($book->title); ?>" required>
        </div>

        <div>
            <label for="author">Autor:</label>
            <input type="text" id="author" name="author" value="<?php echo e($book->author); ?>" required>
        </div>

        <div>
            <label for="release_date">Data de Lançamento:</label>
            <input type="date" id="release_date" name="release_date" value="<?php echo e($book->release_date); ?>" required>
        </div>

        <div>
            <label for="pages">Número de Páginas:</label>
            <input type="number" id="pages" name="pages" value="<?php echo e($book->pages); ?>" required>
        </div>

        <div>
            <label for="available">Disponível:</label>
            <select id="available" name="available">
                <option value="1" <?php if($book->available == 1): ?> selected <?php endif; ?>>Sim</option>
                <option value="0" <?php if($book->available == 0): ?> selected <?php endif; ?>>Não</option>
            </select>
        </div>

        <div>
            <label for="price">Preço:</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo e($book->price); ?>" required>
        </div>

        <button type="submit">Salvar</button>

        <a href="<?php echo e(route('books.index')); ?>">← Voltar para a Lista de Livros</a>
    </form>
</body>
</html>
<?php /**PATH /home/mikai/web2/crudlaravel-01/resources/views/books/edit.blade.php ENDPATH**/ ?>