<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
require_once '../inc/conexao.php';

$stats = [
    'totalEmployees' => 0,
    'averagePerformance' => 0,
    'goalsCompleted' => 0,
    'pendingEvaluations' => 0
];

$res = $conexao->query("SELECT COUNT(*) AS total, AVG(performance_score) AS avg_perf FROM tbPessoas");
if ($res && $row = $res->fetch_assoc()) {
    $stats['totalEmployees'] = (int)$row['total'];
    $stats['averagePerformance'] = $row['avg_perf'] ? round((float)$row['avg_perf'], 1) : 0;
}

$res = $conexao->query("SELECT COUNT(*) AS total FROM tbMetas WHERE status IN ('concluida','completed')");
if ($res && $row = $res->fetch_assoc()) {
    $stats['goalsCompleted'] = (int)$row['total'];
}

$res = $conexao->query("
    SELECT COUNT(*) AS total
    FROM tbAvaliacao a
    LEFT JOIN tbAvaliacaoStatus s ON a.avaliacao_status_id = s.avaliacao_status_id
    WHERE LOWER(COALESCE(s.descricao,'')) LIKE '%pendente%'
");
if ($res && $row = $res->fetch_assoc()) {
    $stats['pendingEvaluations'] = (int)$row['total'];
}

$monthNames = [1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'];
$history = [];
$historyMap = [];
for ($i = 5; $i >= 0; $i--) {
    $dt = new DateTime("first day of -$i month");
    $key = $dt->format('Y-m');
    $label = $monthNames[(int)$dt->format('n')];
    $historyMap[$key] = ['month' => $label, 'score' => 0];
}

$res = $conexao->query("
    SELECT DATE_FORMAT(data, '%Y-%m') AS ym, AVG(pontuacao) AS avg_score
    FROM tbAvaliacao
    WHERE data >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY ym
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $ym = $row['ym'];
        if (isset($historyMap[$ym])) {
            $historyMap[$ym]['score'] = $row['avg_score'] ? round((float)$row['avg_score'], 1) : 0;
        }
    }
}
$history = array_values($historyMap);

$departmentPerformance = [];
$res = $conexao->query("
    SELECT COALESCE(NULLIF(departamento,''), 'Sem departamento') AS depto, AVG(performance_score) AS avg_score
    FROM tbPessoas
    GROUP BY depto
    ORDER BY avg_score DESC
");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $departmentPerformance[] = [
            'name' => $row['depto'],
            'value' => $row['avg_score'] ? round((float)$row['avg_score']) : 0
        ];
    }
}

$topPerformers = [];
$res = $conexao->query("
    SELECT pessoa_id, nome, departamento, cargo, performance_score
    FROM tbPessoas
    ORDER BY performance_score DESC
    LIMIT 5
");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $topPerformers[] = [
            'id' => $row['pessoa_id'],
            'name' => $row['nome'],
            'department' => $row['departamento'],
            'position' => $row['cargo'],
            'performanceScore' => (int)$row['performance_score']
        ];
    }
}

$evaluations = [];
$res = $conexao->query("SELECT avaliacao_id, pontuacao, data FROM tbAvaliacao ORDER BY data DESC LIMIT 6");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $evaluations[] = [
            'id' => $row['avaliacao_id'],
            'score' => (int)$row['pontuacao'],
            'date' => $row['data']
        ];
    }
}

$feedbacks = [];
$res = $conexao->query("SELECT feedback_id, conteudo, data_feedback FROM tbFeedbacks ORDER BY data_feedback DESC LIMIT 6");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $feedbacks[] = [
            'id' => $row['feedback_id'],
            'content' => $row['conteudo'],
            'date' => $row['data_feedback']
        ];
    }
}

$dbDashboardData = [
    'stats' => $stats,
    'performanceHistory' => $history,
    'departmentPerformance' => $departmentPerformance,
    'topPerformers' => $topPerformers,
    'evaluations' => $evaluations,
    'feedbacks' => $feedbacks,
    'goals' => []
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>PerformanceHub — Dashboard</title>
  <link rel="stylesheet" href="../styles/dashboard.css" />
  <link rel="stylesheet" href="../styles/sideBar.css" />
  <link rel="stylesheet" href="../styles/ui.css" />
</head>
<body>
  <div>
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
            <a href="dashboard.php" class="nav-item active">Dashboard</a>
            <a href="employees.php" class="nav-item">Funcionarios</a>
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


  <main class="main">
    <header class="header">
      <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-sub">Visao geral do desempenho da equipe</p>
      </div>
    </header>

    <section class="stats-grid">
      <div class="stat-card">
        <div class="stat-title-black">Total de Funcionarios</div>
        <div class="stat-value-black" id="totalEmployees">0</div>
        <div class="stat-trend-black">Atualizado pelo sistema</div>
      </div>

      <div class="stat-card primary">
        <div class="stat-title">Desempenho Medio</div>
        <div class="stat-value" id="avgPerformance">0%</div>
        <div class="stat-trend">Atualizado pelo sistema</div>
      </div>

      <div class="stat-card success">
        <div class="stat-title">Metas Concluidas</div>
        <div class="stat-value" id="goalsCompleted">0</div>
        <div class="stat-trend">Atualizado pelo sistema</div>
      </div>

      <div class="stat-card">
        <div class="stat-title-black">Avaliacoes Pendentes</div>
        <div class="stat-value-black" id="pendingEvaluations">0</div>
        <div class="stat-trend negative">Atualizado pelo sistema</div>
      </div>
    </section>

    <section class="charts-row">
      <div class="chart-card" id="perfChartCard">
        <h3>Evolucao do Desempenho</h3>
        <div class="chart-area" id="lineChart"></div>
      </div>

      <div class="chart-card" id="deptChartCard">
        <h3>Desempenho por Departamento</h3>
        <div class="chart-area" id="barChart"></div>
      </div>
    </section>

    <section class="lower-row">
      <div class="recent-card" id="recentActivities">
        <h3>Atividades Recentes</h3>
        <div id="activitiesList"></div>
      </div>

      <div class="top-card" id="topPerformers">
        <h3>Top Performers</h3>
        <div id="performersList"></div>
      </div>
    </section>

  </main>
  </div>

  <script>
      window.dbDashboardData = <?php echo json_encode($dbDashboardData); ?>;
  </script>
  <script src="../scripts/dashboard.js"></script>
</body>
</html>
