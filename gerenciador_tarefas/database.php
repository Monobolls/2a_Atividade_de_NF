<?php

$db_file = __DIR__ . '/tarefas.db';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "CREATE TABLE IF NOT EXISTS tarefas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                descricao TEXT NOT NULL,
                data_vencimento TEXT,
                concluida INTEGER DEFAULT 0
              )";
    $pdo->exec($query);

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>