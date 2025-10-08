<html>
<head></head>
<body>    
    <h1>Lista de Livros</h1>        

    <!-- Botão de adicionar livro -->
    <a href="<?php echo e(route('books.create')); ?>">Adicionar Livro</a>

    <?php if($books->isEmpty()): ?>
        <p>Nenhum livro cadastrado.</p>
    <?php else: ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Ações</th>
            </tr>
            <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($book->id); ?></td>
                <td><?php echo e($book->title); ?></td>
                <td><?php echo e($book->author); ?></td>

                <td>
                    <!-- Botão de Visualizar -->
                    <a href="<?php echo e(route('books.show', $book)); ?>">Visualizar</a>

                    <!-- Botão de Editar -->
                    <a href="<?php echo e(route('books.edit', $book)); ?>">Editar</a>

                    <!-- Botão de Excluir -->
                    <form action="<?php echo e(route('books.destroy', $book)); ?>" method="POST" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button onclick="return confirm('Deseja excluir este livro?')">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    <?php endif; ?>
</body>
</html>
<?php /**PATH /home/mikai/web2/crudlaravel-01/resources/views/books/index.blade.php ENDPATH**/ ?>