<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
require_once '../inc/conexao.php';

$employees = [];
$sql = "SELECT pessoa_id, nome, email, cargo, departamento, data_admissao, performance_score, cpf, nascimento, telefone, pessoa_tipo_id FROM tbPessoas ORDER BY nome";
$result = $conexao->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = [
            'id' => $row['pessoa_id'],
            'name' => $row['nome'],
            'email' => $row['email'],
            'position' => $row['cargo'],
            'department' => $row['departamento'],
            'hireDate' => $row['data_admissao'],
            'performanceScore' => (int)$row['performance_score'],
            'cpf' => $row['cpf'],
            'birth' => $row['nascimento'],
            'phone' => $row['telefone'],
            'personTypeId' => $row['pessoa_tipo_id']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Funcionarios</title>
    <link rel="stylesheet" href="../styles/employees.css" />
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
            <a href="employees.php" class="nav-item active">Funcionarios</a>
            <a href="usuario_listar.php" class="nav-item">Usuarios do Sistema</a>
            <a href="goals.php" class="nav-item">Metas</a>
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
                <h1 class="page-title">Funcionarios</h1>
                <p class="page-sub">Gerencie sua equipe</p>
            </div>

            <button id="openModal" class="btn-primary">
                Novo Funcionario
            </button>
        </header>

        <div class="search-area">
            <input id="searchInput" type="text" placeholder="Buscar por nome, cargo ou departamento..." />
        </div>

        <div id="employeesGrid" class="employees-grid"></div>

        <div id="emptyState" class="empty hidden">
            <p>Nenhum funcionario encontrado.</p>
            <span>Tente ajustar os filtros de busca.</span>
        </div>

    </main>

    <div id="modal" class="modal hidden">
        <div class="modal-content">
            <h2 id="modalTitle">Adicionar Funcionario</h2>

            <input id="empId" type="hidden" />

            <label>Nome completo</label>
            <input id="empName" type="text" placeholder="Nome do funcionario" />

            <label>Email</label>
            <input id="empEmail" type="email" placeholder="email@empresa.com" />

            <label>CPF</label>
            <input id="empCpf" type="text" placeholder="000.000.000-00" />

            <label>Nascimento</label>
            <input id="empBirth" type="date" />

            <label>Telefone</label>
            <input id="empPhone" type="text" placeholder="(00) 00000-0000" />

            <label>Cargo</label>
            <input id="empPosition" type="text" placeholder="Ex: Desenvolvedor Senior" />

            <label>Departamento</label>
            <select id="empDepartment">
                <option selected disabled>Selecione...</option>
                <option>Tecnologia</option>
                <option>Design</option>
                <option>Marketing</option>
                <option>Recursos Humanos</option>
                <option>Operacoes</option>
                <option>Financeiro</option>
            </select>

            <label>Data de admissao</label>
            <input id="empHireDate" type="date" />

            <label>Performance (0 a 100)</label>
            <input id="empPerformance" type="number" min="0" max="100" value="0" />

            <button id="saveEmployee" class="btn-primary">Adicionar</button>
            <button id="closeModal" class="btn-secondary">Cancelar</button>
        </div>
    </div>

    <script>
        window.dbEmployees = <?php echo json_encode($employees); ?>;
    </script>
    <script src="../scripts/employees.js"></script>
</body>
</html>
