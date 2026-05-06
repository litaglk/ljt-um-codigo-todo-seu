<!DOCTYPE html>
<html lang="pt-BR">
<!-- ... (Mantenham a tag <head> e o CSS antigo aqui) ... -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ljt - um código todo seu</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #000000; color: #0b3d00; display: flex; justify-content: center; padding-top: 50px; }
        .container { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h1 { font-size: 1.5rem; text-align: center; border-bottom: 2px solid #000000; padding-bottom: 10px; }
        .error { color: #dc2626; background: #000000; padding: 10px; border-radius: 4px; font-size: 0.9rem; }
        .form-group { display: flex; flex-direction: column; gap: 15px; margin-top: 20px; margin-bottom: 20px; }
        input[type="text"] { flex: 1; padding: 10px; border: 1px solid #000000; border-radius: 4px; }
        button { background: #092e00; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #25900A; }
        ul { list-style: none; padding: 0; }
        li { display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #000000; }
        li.done span { text-decoration: line-through; color: #000000; }
        .actions a { text-decoration: none; margin-left: 10px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h1>LT: um código todo seu</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" class="form-group">
        <input type="text" name="title" required autocomplete="off">
        <textarea name="description" placeholder="o que precisa ser feito?"></textarea>
        <button type="submit">Adicionar</button>

        <div class="input-row">
            <input type="date" name="due_date" style="flex: 1;", required title="Data de Vencimento">
            <input type="text" name="assigned_to" placeholder="pessoa responsável" style="flex: 1;" required>
        </div>
        <button type="submit">Cadastrar Tarefa</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <span><?php echo htmlspecialchars($task['title']); ?></span>

                <?php if ($task['description']): ?>
                    <div class="task-info"><?php echo htmlspecialchars($task['description']); ?></div>

                                    <?php endif; ?>
                
                <div class="task-meta">
                    Vence em: <?php echo date('d/m/Y', strtotime($task['due_date'])); ?> Responsável: <?php echo htmlspecialchars($task['assigned_to']); ?>
                </div>

                <div class="actions">
                    <?php if (!$task['done']): ?>
                        <a href="?action=complete&id=<?php echo $task['id']; ?>" title="Concluir"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#061f00"><path d="M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z"/></svg></a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#061f00"><path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z"/></svg></a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>
