<html>
<head></head>
<body>    
    <h1>Detalhes do Livro</h1>        
    <div>
        <p><strong>ID:</strong> <?php echo e($book->id); ?></p>
        <p><strong>Título:</strong> <?php echo e($book->title); ?></p>
        <p><strong>Autor:</strong> <?php echo e($book->author); ?></p>
        <p><strong>Data de Lançamento:</strong> <?php echo e($book->release_date); ?></p>
        <p><strong>Número de Páginas:</strong> <?php echo e($book->pages); ?></p>
        <p><strong>Disponível:</strong> <?php echo e($book->available ? 'Sim' : 'Não'); ?></p>
        <p><strong>Preço:</strong> R$ <?php echo e(number_format($book->price, 2, ',', '.')); ?></p>
    </div>

    <a href="<?php echo e(route('books.index')); ?>">← Voltar para a Lista de Livros</a>
</body>
</html>
<?php /**PATH /home/mikai/web2/crudlaravel-01/resources/views/books/show.blade.php ENDPATH**/ ?>