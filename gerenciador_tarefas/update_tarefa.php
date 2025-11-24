<?php
require_once 'database.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id === false) {
        die("ID inválido.");
    }

    try {
        
        $stmt = $pdo->prepare("UPDATE tarefas SET concluida = 1 WHERE id = :id");
        
        $stmt->execute([':id' => $id]);

        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        die("Erro ao atualizar tarefa: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>