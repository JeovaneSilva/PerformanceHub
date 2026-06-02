<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
require_once '../inc/conexao.php';

$employees = [];
$sqlEmp = "SELECT pessoa_id, nome FROM tbPessoas ORDER BY nome";
$resultEmp = $conexao->query($sqlEmp);
if ($resultEmp && $resultEmp->num_rows > 0) {
    while ($row = $resultEmp->fetch_assoc()) {
        $employees[] = [
            'id' => $row['pessoa_id'],
            'name' => $row['nome']
        ];
    }
}

$goals = [];
$sqlGoals = "SELECT meta_id, funcionario_id, titulo, descricao, data_limite, status, progresso FROM tbMetas ORDER BY data_limite DESC";
$resultGoals = $conexao->query($sqlGoals);
if ($resultGoals && $resultGoals->num_rows > 0) {
    while ($row = $resultGoals->fetch_assoc()) {
        $goals[] = [
            'id' => $row['meta_id'],
            'employeeId' => $row['funcionario_id'],
            'title' => $row['titulo'],
            'desc' => $row['descricao'],
            'date' => $row['data_limite'],
            'progress' => (int)$row['progresso'],
            'status' => $row['status']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Metas</title>
    <link rel="stylesheet" href="../styles/goals.css" />
    <link rel="stylesheet" href="../styles/sideBar.css" />
    <link rel="stylesheet" href="../styles/ui.css" />
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-top">
            <div class="logo">
                <div class="logo-text">
                    <div class="app-title">PerformanceHub</div>
                    <div class="app-sub">Gestao de Desempenho</div>
                </div>
            </div>
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="employees.php" class="nav-item">Funcionarios</a>
            <a href="usuario_listar.php" class="nav-item">Usuarios do Sistema</a>
            <a href="goals.php" class="nav-item active">Metas</a>
            <a href="evaluations.php" class="nav-item">Avaliacoes</a>
            <a href="feedbacks.php" class="nav-item">Feedbacks</a>
        </nav>

        <div class="sidebar-bottom">
            <div class="user">
                <div class="avatar">J</div>
                <div class="user-info">
                    <div class="user-name" id="userName">Admin</div>
                    <div class="user-email" id="userEmail"><?php echo $_SESSION['usuario_login'] ?? 'email@exemplo.com'; ?></div>
                </div>
            </div>
            <button onclick="window.location.href='../pages/login.php'" class="logout">Sair</button>
        </div>
    </aside>

    <main class="content">

        <header class="header">
            <div>
                <h1 class="page-title">Metas</h1>
                <p class="page-sub">Acompanhe as metas da equipe</p>
            </div>
            <button id="openModal" class="btn-primary">Nova Meta</button>
        </header>

        <div class="filters">
            <button class="filter-btn active" data-filter="all">Todas</button>
            <button class="filter-btn" data-filter="pendente">Pendente</button>
            <button class="filter-btn" data-filter="em_andamento">Em Andamento</button>
            <button class="filter-btn" data-filter="concluida">Concluida</button>
            <button class="filter-btn" data-filter="cancelada">Cancelada</button>
        </div>

        <div id="goalsList" class="goals-list"></div>

        <div id="modal" class="modal hidden">
            <div class="modal-content">
                <h2 id="modalTitle">Criar Nova Meta</h2>

                <label>Funcionario</label>
                <select id="employeeSelect">
                    <option value="">Selecione</option>
                    <?php foreach ($employees as $emp) { ?>
                        <option value="<?php echo htmlspecialchars($emp['id']); ?>">
                            <?php echo htmlspecialchars($emp['name']); ?>
                        </option>
                    <?php } ?>
                </select>

                <label>Titulo da Meta</label>
                <input id="goalTitle" type="text" placeholder="Ex: Aumentar vendas em 20%">

                <label>Descricao</label>
                <textarea id="goalDesc" placeholder="Descricao..."></textarea>

                <label>Data limite</label>
                <input id="goalDate" type="date">

                <button id="saveGoal" class="btn-primary w-full">Criar Meta</button>
                <button id="closeModal" class="btn-secondary w-full mt-1">Cancelar</button>
            </div>
        </div>

    </main>

    <script>
        window.dbEmployees = <?php echo json_encode($employees); ?>;
        window.dbGoals = <?php echo json_encode($goals); ?>;
    </script>
    <script src="../scripts/goals.js"></script>
</body>
</html>
