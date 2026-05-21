<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
require_once '../inc/conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Savir Sistemas - Usuários</title>
    <link rel="stylesheet" href="../styles/employees.css" />
    <link rel="stylesheet" href="../styles/sideBar.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function editar() {
            const marcados = document.querySelectorAll('input[name="sel[]"]:checked');
            if (marcados.length === 0) {
                alert("Selecione um registro para editar.");
                return;
            }
            if (marcados.length > 1) {
                alert("Selecione apenas um registro para editar.");
                return;
            }
            const id = marcados[0].value;
            window.location.href = "usuario_editar.php?usuario_id=" + id;
        }

        function excluir() {
            const marcados = document.querySelectorAll('input[name="sel[]"]:checked');
            if (marcados.length === 0) {
                alert("Selecione um registro para excluir.");
                return;
            }

            if (confirm("Tem certeza que deseja excluir os usuários selecionados?")) {
                let idsExcluir = [];
                marcados.forEach(cb => idsExcluir.push(cb.value));

                $.ajax({
                    url: '../scripts/acao_excluir.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { pIds: idsExcluir },
                    success: function(data) {
                        if (data.status === "success") {
                            alert(data.message);
                            location.reload(); 
                        } else {
                            alert(data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Erro na requisição de exclusão.");
                    }
                });
            }
        }

        function toggleCheckTodos() {
            const checkTodos = document.getElementById('checkTodos');
            const checkboxes = document.querySelectorAll('input[name="sel[]"]');
            checkboxes.forEach(cb => cb.checked = checkTodos.checked);
        }
    </script>

    <style>
        .table-container { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-top: 20px; overflow-x: auto; }
        .modern-table { width: 100%; border-collapse: collapse; text-align: left; }
        .modern-table th { padding: 16px; background-color: #f8fafc; color: #475569; font-weight: 600; border-bottom: 2px solid #e2e8f0; }
        .modern-table td { padding: 16px; color: #334155; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .modern-table tr:hover { background-color: #f8fafc; }
        .action-btns { display: flex; gap: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="logo">
                <div class="logo-text">
                    <div class="app-title">PerformanceHub</div>
                    <div class="app-sub">Gestão de Desempenho</div>
                </div>
            </div>
        </div>
        <nav class="nav">
            <a href="dashboard.html" class="nav-item">Dashboard</a>
            <a href="employees.html"class="nav-item">Funcionários</a>
            <a href="usuario_listar.php" class="nav-item active">Usuários do Sistema</a> 
            <a href="goals.html" class="nav-item ">Metas</a>
            <a href="evaluations.html" class="nav-item">Avaliações</a>
            <a href="feedbacks.html" class="nav-item">Feedbacks</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user">
                <div class="avatar">J</div>
                <div class="user-info">
                    <div class="user-name">Admin</div>
                    <div class="user-email"><?php echo $_SESSION['usuario_login'] ?? 'email@exemplo.com'; ?></div>
                </div>
            </div>
            <button onclick="window.location.href='../pages/login.php'" class="logout">Sair</button>
        </div>
    </aside>

    <main class="content">
        <header class="header">
            <div>
                <h1 class="page-title">Usuários</h1>
                <p class="page-sub">Gerencie os acessos ao PerformanceHub</p>
            </div>
        </header>

        <div class="action-btns">
            <button type="button" class="btn-secondary" onclick="javascript:editar();">Editar Selecionado</button>
            <button type="button" class="btn-secondary" style="background: #fee2e2; color: #b91c1c; border-color: #fca5a5;" onclick="javascript:excluir();">Excluir Selecionados</button>
        </div>

        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 50px;"><input type="checkbox" id="checkTodos" onclick="toggleCheckTodos()"></th>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail (Login)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT usuario_id, nome, login FROM tbUsuarios ORDER BY nome";
                    $result = $conexao->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $usuario_id = htmlspecialchars($row['usuario_id']);
                            $nome = htmlspecialchars($row['nome']);
                            $login = htmlspecialchars($row['login']);

                            echo '<tr>';
                            echo '<td><input type="checkbox" name="sel[]" value="' . $usuario_id . '"></td>';
                            echo '<td>' . $usuario_id . '</td>';
                            echo '<td style="font-weight: 600;">' . $nome . '</td>';
                            echo '<td style="color: #64748b;">' . $login . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4" style="text-align: center; color: #64748b; padding: 30px;">Nenhum usuário encontrado</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>