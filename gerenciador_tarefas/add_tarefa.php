<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $descricao = trim($_POST['descricao'] ?? '');
    $data_vencimento = $_POST['data_vencimento'] ?? null;

    if (empty($descricao)) {

        header("Location: index.php?erro=DescricaoObrigatoria");
        exit;
    }

    if(empty($data_vencimento)) {
        $data_vencimento = null;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO tarefas (descricao, data_vencimento) VALUES (:desc, :data)");
        
        $stmt->execute([
            ':desc' => $descricao,
            ':data' => $data_vencimento
        ]);

        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        die("Erro ao adicionar tarefa: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>