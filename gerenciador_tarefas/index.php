<?php
require_once __DIR__ . '/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM tarefas ORDER BY data_vencimento ASC");
    $todasTarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar tarefas: " . $e->getMessage());
}

$pendentes = array_filter($todasTarefas, function($t) { return $t['concluida'] == 0; });
$concluidas = array_filter($todasTarefas, function($t) { return $t['concluida'] == 1; });

function formatarData($dataIso) {
    if(empty($dataIso)) return 'Sem data';
    return date('d/m/Y', strtotime($dataIso));
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="light"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Tarefas Moderno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>

        .task-card { transition: all 0.2s; border-left: 5px solid #0d6efd; }
        
        .task-card-done { border-left: 5px solid #198754; opacity: 0.7; background-color: #e9ecef; }
        .concluida-texto { text-decoration: line-through; color: #6c757d; }

        [data-bs-theme="dark"] .task-card-done {
             background-color: rgba(255, 255, 255, 0.05) !important; /* Fundo sutilmente mais claro que o card escuro */
             border-color: #495057;
        }
        
        [data-bs-theme="dark"] .main-card, 
        [data-bs-theme="dark"] .task-card {
            background-color: var(--bs-gray-800);
            border-color: var(--bs-gray-700);
        }
    </style>
</head>
<body class="position-relative">

<div class="position-absolute top-0 end-0 p-3 mt-2">
    <button class="btn btn-outline-secondary rounded-circle shadow-sm" id="theme-toggle" title="Alternar tema Claro/Escuro">
        <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
    </button>
</div>

<div class="container py-5" style="max-width: 900px;">
    <h1 class="text-center mb-4 display-5 fw-bold text-primary"><i class="bi bi-check2-square"></i> Minhas Tarefas</h1>

    <div class="card shadow-sm mb-5 main-card">
        <div class="card-body rounded">
            <h5 class="card-title mb-3">Nova Tarefa</h5>
            <form action="add_tarefa.php" method="POST" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" required placeholder="O que precisa ser feito?">
                </div>
                <div class="col-md-4">
                    <label for="data_vencimento" class="form-label">Vencimento</label>
                    <input type="date" class="form-control" id="data_vencimento" name="data_vencimento">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Adicionar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <h3 class="text-primary mb-3">Pendentes (<?= count($pendentes) ?>)</h3>
            <?php if (empty($pendentes)): ?>
                <div class="alert alert-info">Nenhuma tarefa pendente.</div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                <?php foreach ($pendentes as $tarefa): ?>
                    <div class="card shadow-sm task-card">
                        <div class="card-body d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h5 class="card-title mb-1"><?= htmlspecialchars($tarefa['descricao']) ?></h5>
                                <small class="text-body-secondary"> <i class="bi bi-calendar"></i> <?= formatarData($tarefa['data_vencimento']) ?>
                                </small>
                            </div>
                            <div class="btn-group">
                                <a href="update_tarefa.php?id=<?= $tarefa['id'] ?>" class="btn btn-sm btn-outline-success" title="Marcar como concluída">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                                <a href="delete_tarefa.php?id=<?= $tarefa['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6 mb-4">
             <h3 class="text-success mb-3">Concluídas (<?= count($concluidas) ?>)</h3>
             <?php if (empty($concluidas)): ?>
                <div class="text-body-secondary">Nenhuma tarefa concluída ainda.</div> <?php else: ?>
                 <div class="d-flex flex-column gap-2">
                <?php foreach ($concluidas as $tarefa): ?>
                    <div class="card shadow-sm task-card-done">
                        <div class="card-body d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h5 class="card-title mb-1 concluida-texto"><?= htmlspecialchars($tarefa['descricao']) ?></h5>
                                <small class="text-body-secondary"> Concluída
                                </small>
                            </div>
                            <div>
                                <a href="delete_tarefa.php?id=<?= $tarefa['id'] ?>" class="btn btn-sm btn-outline-secondary btn-delete" title="Excluir permanentemente">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if(!confirm('Tem certeza que deseja excluir esta tarefa?')) {
                e.preventDefault();
            }
        });
    });

    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const htmlElement = document.documentElement;

    function setTheme(theme) {
        htmlElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);

        if (theme === 'dark') {
            themeIcon.classList.remove('bi-moon-stars-fill');
            themeIcon.classList.add('bi-sun-fill');
        } else {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-stars-fill');
        }
    }

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme);
    } else {
        setTheme('light');
    }

    themeToggleBtn.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        setTheme(newTheme);
    });
</script>
</body>
</html>