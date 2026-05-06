<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS E CRIAÇÃO DA TABELA
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL,
    assigned_to TEXT NOT NULL,
    done INTEGER DEFAULT 0
)");

// 2. LÓGICA DE NEGÓCIO E CONTROLE DE REQUISIÇÕES
$error = '';

// Criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = trim($_POST['due_date']);
    $assigned_to = trim($_POST['assigned_to']);
    
    if (empty($title) || empty($due_date) || empty($assigned_to)){
    // Regra de negócio solta no meio do arquivo
        $error = "O título, a data de vencimento e a pessoa responsável são obrigatórios!";
    } else {
        $sql = "INSERT INTO tasks (title, description, due_date, assigned_to) VALUES (:title, :description, :due_date, :assigned_to)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $due_date);
        $stmt->bindValue(':assigned_to', $assigned_to);
        $stmt->execute();
        
        // Redirecionamento misturado com a lógica
        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }
    
    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS MISTURADA COM A VISUALIZAÇÃO
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY due_date ASC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
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

