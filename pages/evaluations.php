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

$users = [];
$sqlUsers = "SELECT usuario_id, nome FROM tbUsuarios ORDER BY nome";
$resultUsers = $conexao->query($sqlUsers);
if ($resultUsers && $resultUsers->num_rows > 0) {
    while ($row = $resultUsers->fetch_assoc()) {
        $users[] = [
            'id' => $row['usuario_id'],
            'name' => $row['nome']
        ];
    }
}

$evaluations = [];
$sql = "
    SELECT a.avaliacao_id, a.data, a.tipo, a.pontuacao, a.avaliador_nome, a.observacao,
           a.funcionario_id, a.avaliacao_status_id,
           p.nome AS funcionario_nome
    FROM tbAvaliacao a
    LEFT JOIN tbPessoas p ON a.funcionario_id = p.pessoa_id
    ORDER BY a.data DESC, a.avaliacao_id DESC
";
$result = $conexao->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $evaluations[] = [
            'id' => $row['avaliacao_id'],
            'employeeId' => $row['funcionario_id'],
            'statusId' => $row['avaliacao_status_id'],
            'employeeName' => $row['funcionario_nome'],
            'type' => $row['tipo'],
            'score' => (int)$row['pontuacao'],
            'evaluatorName' => $row['avaliador_nome'],
            'comments' => $row['observacao'],
            'date' => $row['data']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Avaliacoes</title>

    <link rel="stylesheet" href="../styles/evaluations.css">
    <link rel="stylesheet" href="../styles/sideBar.css">
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
            <a href="goals.php" class="nav-item">Metas</a>
            <a href="evaluations.php" class="nav-item active">Avaliacoes</a>
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
                <h1 class="page-title">Avaliacoes</h1>
                <p class="page-sub">Registre e acompanhe avaliacoes de desempenho</p>
            </div>
            <button id="openModalBtn" class="btn-primary">Nova Avaliacao</button>
        </header>

        <section id="evaluationsList" class="list"></section>
     </main>

    <div id="modal" class="modal hidden">
        <div class="modal-content">
            <h2 id="modalTitle">Registrar Avaliacao</h2>

            <label>Funcionario</label>
            <select id="employeeId">
                <option value="">Selecione...</option>
                <?php foreach ($employees as $emp) { ?>
                    <option value="<?php echo htmlspecialchars($emp['id']); ?>">
                        <?php echo htmlspecialchars($emp['name']); ?>
                    </option>
                <?php } ?>
            </select>

            <label>Tipo</label>
            <select id="evaluationType">
                <option value="mensal">Mensal</option>
                <option value="trimestral">Trimestral</option>
                <option value="anual">Anual</option>
            </select>

            <label>Pontuacao (0 a 100)</label>
            <input type="number" id="score" min="0" max="100">

            <label>Avaliador</label>
            <select id="evaluatorId">
                <option value="">Selecione...</option>
                <?php foreach ($users as $user) { ?>
                    <option value="<?php echo htmlspecialchars($user['id']); ?>">
                        <?php echo htmlspecialchars($user['name']); ?>
                    </option>
                <?php } ?>
            </select>

            <label>Comentarios</label>
            <textarea id="comments" placeholder="Observacoes..."></textarea>

            <button id="saveEvaluationBtn" class="btn-primary">Salvar</button>
            <button id="closeModalBtn" class="btn-secondary">Cancelar</button>
        </div>
    </div>

    <script>
        window.dbEmployees = <?php echo json_encode($employees); ?>;
        window.dbUsers = <?php echo json_encode($users); ?>;
        window.dbEvaluations = <?php echo json_encode($evaluations); ?>;
    </script>
    <script src="../scripts/evaluations.js"></script>
</body>
</html>
