<?php
require_once 'database.php';

$livros = [];
$error_message = null;
try {
    $stmt = $pdo->query("SELECT id, titulo, autor, ano FROM livros ORDER BY titulo ASC");
    $livros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erro ao listar livros: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco de Dados Livraria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 border-b-4 border-blue-600 pb-2">
            Catálogo de Livros
        </h1>

        <?php if (isset($_GET['status'])): ?>
            <?php
            $status = $_GET['status'];
            $message = $_GET['message'] ?? '';
            $class = "p-4 mb-6 rounded-lg font-medium";
            
            if ($status == 'added' || $status == 'deleted') {
                $class .= " bg-green-100 text-green-800 border border-green-400";
                $text = ($status == 'added') ? "Livro adicionado com sucesso!" : "Livro excluído com sucesso!";
            } elseif ($status == 'error') {
                $class .= " bg-red-100 text-red-800 border border-red-400";
                $text = "Erro na Operação: " . htmlspecialchars($message);
            }
            ?>
            <div class="<?= $class ?>" role="alert"><?= $text ?></div>
        <?php elseif ($error_message): ?>
            <div class="p-4 mb-6 rounded-lg font-medium bg-red-100 text-red-800 border border-red-400" role="alert">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 rounded-xl shadow-lg mb-10 border border-blue-100">
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">a) Adicionar Livro</h2>
            
            <form action="add_book.php" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-1">
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" id="titulo" name="titulo" required 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-2 border focus:border-blue-500">
                </div>
                <div class="md:col-span-1">
                    <label for="autor" class="block text-sm font-medium text-gray-700">Autor</label>
                    <input type="text" id="autor" name="autor" required 
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-2 border focus:border-blue-500">
                </div>
                <div class="md:col-span-1">
                    <label for="ano" class="block text-sm font-medium text-gray-700">Ano</label>
                    <input type="number" id="ano" name="ano" required min="1000" max="<?= date('Y') + 1 ?>"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm p-2 border focus:border-blue-500">
                </div>
                <div class="md:col-span-1">
                    <button type="submit" 
                            class="w-full py-2 px-4 rounded-lg shadow-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out font-medium">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-4">b) Livros Cadastrados</h2>
        
        <?php if (empty($livros)): ?>
            <p class="text-gray-500 p-6 bg-white rounded-xl shadow-inner border">O catálogo está vazio. Adicione um livro acima!</p>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ano</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">c) Ação</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($livros as $livro): ?>
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($livro['id']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"><?= htmlspecialchars($livro['titulo']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($livro['autor']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($livro['ano']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <!-- Botão de Excluir que chama a função JavaScript -->
                                    <button onclick="confirmDeletion(<?= $livro['id'] ?>, '<?= addslashes(htmlspecialchars($livro['titulo'])) ?>')"
                                            class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out px-3 py-1 bg-red-50 rounded-lg text-xs font-bold shadow-sm hover:shadow-md">
                                        Excluir (<?= $livro['id'] ?>)
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        /**
         */
        function confirmDeletion(id, title) {
            const modalHtml = `
                <div id="delete-modal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm transform transition-all duration-300 scale-100">
                        <h3 class="text-xl font-bold text-red-700 mb-3">Atenção!</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            Você está prestes a excluir o livro: 
                            <span class="font-semibold text-gray-800 block mt-1">"${title}" (ID: ${id})</span>.
                            Confirma a exclusão?
                        </p>
                        <div class="flex justify-end space-x-3">
                            <button onclick="document.getElementById('delete-modal').remove()"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                                Cancelar
                            </button>
                            <!-- Redireciona para o script de exclusão -->
                            <a href="delete_book.php?id=${id}"
                               class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-md">
                                Sim, Excluir Livro
                            </a>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
    </script>
</body>
</html>