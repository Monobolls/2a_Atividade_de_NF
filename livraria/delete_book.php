<?php
require_once 'database.php';

$id = $_GET['id'] ?? null;

if (!empty($id)) {
    $id = (int)$id;

    try {
        $stmt = $pdo->prepare("DELETE FROM livros WHERE id = :id");
        
        $stmt->bindParam(':id', $id);
        
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            header("Location: index.php?status=deleted");
        } else {
            header("Location: index.php?status=error&message=" . urlencode("Livro com ID {$id} não encontrado."));
        }
        exit();

    } catch (PDOException $e) {
        header("Location: index.php?status=error&message=" . urlencode("Erro ao excluir livro: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: index.php?status=error&message=" . urlencode("ID do livro não fornecido para exclusão."));
    exit();
}
?>