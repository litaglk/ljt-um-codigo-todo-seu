<?php
// Substitua todo o bloco spl_autoload_register por:
require_once __DIR__ . '/vendor/autoload.php';

// Importe as classes que você vai instanciar
use App\Model\Task;
use App\Presenter\TaskPresenter;
use App\View\TaskHtmlView;

$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Instanciamos o Model
$model = new Task($pdo);

// 2. Instanciamos a View (que implementa TaskViewInterface)
$view = new TaskHtmlView();

// 3. Instanciamos o Presenter, injetando as dependências
$presenter = new TaskPresenter($model, $view);

// Roteamento
$action = $_GET['action'] ?? 'index';

if ($action === 'create') {
    $presenter->create($_POST['title'] ?? '', $_POST['description'] ?? '', $_POST['due_date'] ?? '');
} elseif ($action === 'complete') {
    $presenter->complete($_GET['id']);
} elseif ($action === 'delete') {
    $presenter->delete($_GET['id']);
} else {
    $presenter->index();
}
?>