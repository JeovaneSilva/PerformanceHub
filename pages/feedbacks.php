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

$feedbacks = [];
$sql = "
  SELECT f.feedback_id, f.funcionario_id, f.autor_id, f.tipo, f.conteudo, f.data_feedback, f.autor_nome,
       p.nome AS funcionario_nome,
       u.nome AS autor_usuario
    FROM tbFeedbacks f
    LEFT JOIN tbPessoas p ON f.funcionario_id = p.pessoa_id
    LEFT JOIN tbUsuarios u ON f.autor_id = u.usuario_id
    ORDER BY f.data_feedback DESC, f.feedback_id DESC
";
$result = $conexao->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $autor = $row['autor_nome'] ?? '';
        if ($autor == '' && $row['autor_usuario']) {
            $autor = $row['autor_usuario'];
        }
        $feedbacks[] = [
            'id' => $row['feedback_id'],
          'employeeId' => $row['funcionario_id'],
          'authorId' => $row['autor_id'],
            'employee' => $row['funcionario_nome'],
            'author' => $autor,
            'type' => $row['tipo'],
            'content' => $row['conteudo'],
            'date' => $row['data_feedback']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Feedbacks</title>

    <link rel="stylesheet" href="../styles/feedbacks.css" />
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
        <a href="goals.php" class="nav-item">Metas</a>
        <a href="evaluations.php" class="nav-item">Avaliacoes</a>
        <a href="feedbacks.php" class="nav-item active">Feedbacks</a>
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
          <h1 class="page-title">Feedbacks</h1>
          <p class="page-sub">Registre feedbacks periodicos para a equipe</p>
        </div>
        <button id="openModalBtn" class="btn-primary">Novo Feedback</button>
      </header>

      <div id="modal" class="modal hidden">
        <div class="modal-content">
          <h2 id="modalTitle">Novo Feedback</h2>

          <label>Funcionario</label>
          <select id="fbEmployeeId">
            <option value="">Selecione...</option>
            <?php foreach ($employees as $emp) { ?>
              <option value="<?php echo htmlspecialchars($emp['id']); ?>">
                <?php echo htmlspecialchars($emp['name']); ?>
              </option>
            <?php } ?>
          </select>

          <label>Seu Nome</label>
          <select id="fbAuthorId">
            <option value="">Selecione...</option>
            <?php foreach ($users as $user) { ?>
              <option value="<?php echo htmlspecialchars($user['id']); ?>">
                <?php echo htmlspecialchars($user['name']); ?>
              </option>
            <?php } ?>
          </select>

          <label>Tipo</label>
          <select id="fbType">
            <option value="" selected disabled>Selecione...</option>
            <option value="positive">Positivo</option>
            <option value="constructive">Construtivo</option>
            <option value="neutral">Neutro</option>
          </select>

          <label>Feedback</label>
          <textarea
            id="fbContent"
            rows="4"
            placeholder="Escreva o feedback"
          ></textarea>

          <button id="saveFeedback" class="btn-primary">Enviar</button>
          <button id="closeModalBtn" class="btn-secondary">Cancelar</button>
        </div>
      </div>

      <div id="feedbackList" class="feedbacks-list"></div>
    </main>

    <script>
        window.dbEmployees = <?php echo json_encode($employees); ?>;
        window.dbUsers = <?php echo json_encode($users); ?>;
        window.dbFeedbacks = <?php echo json_encode($feedbacks); ?>;
    </script>
    <script src="../scripts/feedbacks.js"></script>
  </body>
</html>
