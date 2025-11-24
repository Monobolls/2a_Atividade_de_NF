<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $titulo = trim($_POST['titulo'] ?? '');
    $autor  = trim($_POST['autor'] ?? '');
    $ano    = (int)($_POST['ano'] ?? 0);

    if (!empty($titulo) && !empty($autor) && $ano > 1000) {
        try {
            $stmt = $pdo->prepare("INSERT INTO livros (titulo, autor, ano) VALUES (:titulo, :autor, :ano)");
            
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':autor', $autor);
            $stmt->bindParam(':ano', $ano);
            
            $stmt->execute();
            
            header("Location: index.php?status=added");
            exit();

        } catch (PDOException $e) {
            header("Location: index.php?status=error&message=" . urlencode("Erro de SQL: " . $e->getMessage()));
            exit();
        }
    } else {
        header("Location: index.php?status=error&message=" . urlencode("Por favor, preencha todos os campos corretamente."));
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>