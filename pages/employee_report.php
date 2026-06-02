<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}
require_once '../inc/conexao.php';

date_default_timezone_set('America/Fortaleza');

function safe_text($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function format_date($value) {
    if (!$value) {
        return '-';
    }
    $timestamp = strtotime($value);
    if (!$timestamp) {
        return $value;
    }
    return date('d/m/Y', $timestamp);
}

function format_status($status) {
    if (!$status) {
        return '-';
    }
    $label = str_replace('_', ' ', $status);
    return ucwords($label);
}

$employee_id_raw = $_GET['id'] ?? '';
$employee_id = null;
$error = '';

if ($employee_id_raw === '' || !ctype_digit($employee_id_raw)) {
    $error = 'Funcionario invalido.';
} else {
    $employee_id = (int)$employee_id_raw;
}

$employee = null;
$goals = [];
$evaluations = [];
$feedbacks = [];

if ($error === '') {
    $stmt = $conexao->prepare(
        "SELECT pessoa_id, nome, email, cargo, departamento, data_admissao, performance_score, cpf, nascimento, telefone
         FROM tbPessoas
         WHERE pessoa_id = ?"
    );

    if (!$stmt) {
        $error = 'Erro ao carregar funcionario.';
    } else {
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        $stmt->close();

        if (!$employee) {
            $error = 'Funcionario nao encontrado.';
        }
    }
}

if ($error === '') {
    $stmt = $conexao->prepare(
        "SELECT meta_id, titulo, descricao, data_limite, status, progresso
         FROM tbMetas
         WHERE funcionario_id = ?
         ORDER BY data_limite DESC, meta_id DESC"
    );

    if ($stmt) {
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $goals[] = $row;
        }
        $stmt->close();
    }
}

if ($error === '') {
    $stmt = $conexao->prepare(
        "SELECT a.avaliacao_id, a.data, a.tipo, a.pontuacao, a.avaliador_nome, a.observacao,
                s.descricao AS status
         FROM tbAvaliacao a
         LEFT JOIN tbAvaliacaoStatus s ON a.avaliacao_status_id = s.avaliacao_status_id
         WHERE a.funcionario_id = ?
         ORDER BY a.data DESC, a.avaliacao_id DESC"
    );

    if ($stmt) {
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $evaluations[] = $row;
        }
        $stmt->close();
    }
}

if ($error === '') {
    $stmt = $conexao->prepare(
        "SELECT f.feedback_id, f.tipo, f.conteudo, f.data_feedback, f.autor_nome, u.nome AS autor_usuario
         FROM tbFeedbacks f
         LEFT JOIN tbUsuarios u ON f.autor_id = u.usuario_id
         WHERE f.funcionario_id = ?
         ORDER BY f.data_feedback DESC, f.feedback_id DESC"
    );

    if ($stmt) {
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $autor = $row['autor_nome'] ?? '';
            if ($autor === '' && $row['autor_usuario']) {
                $autor = $row['autor_usuario'];
            }
            $row['autor_exibicao'] = $autor;
            $feedbacks[] = $row;
        }
        $stmt->close();
    }
}

$conexao->close();
$generated_at = date('d/m/Y H:i');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Relatorio do Funcionario</title>
    <link rel="stylesheet" href="../styles/ui.css" />
    <link rel="stylesheet" href="../styles/report.css" />
</head>
<body>
    <div class="page">
        <div class="report-header">
            <div>
                <h1>Relatorio do funcionario</h1>
                <div class="subtitle">Gerado em <?php echo safe_text($generated_at); ?></div>
            </div>
            <div class="no-print">
                <button class="btn-primary" onclick="window.print()">Imprimir / Salvar PDF</button>
                <a class="btn-secondary" href="employees.php">Voltar</a>
            </div>
        </div>

        <?php if ($error !== '') { ?>
            <div class="section">
                <p class="muted"><?php echo safe_text($error); ?></p>
            </div>
        <?php } else { ?>
            <div class="section">
                <h2>Funcionario</h2>
                <div class="summary-grid">
                    <div class="summary-card">
                        <span class="label">Nome</span>
                        <span class="value"><?php echo safe_text($employee['nome']); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">Email</span>
                        <span class="value"><?php echo safe_text($employee['email']); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">Cargo</span>
                        <span class="value"><?php echo safe_text($employee['cargo']); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">Departamento</span>
                        <span class="value"><?php echo safe_text($employee['departamento']); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">Data de admissao</span>
                        <span class="value"><?php echo safe_text(format_date($employee['data_admissao'])); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">Performance</span>
                        <span class="value"><?php echo safe_text((string)(int)($employee['performance_score'] ?? 0)); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">CPF</span>
                        <span class="value"><?php echo safe_text($employee['cpf']); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">Nascimento</span>
                        <span class="value"><?php echo safe_text(format_date($employee['nascimento'])); ?></span>
                    </div>
                    <div class="summary-card">
                        <span class="label">Telefone</span>
                        <span class="value"><?php echo safe_text($employee['telefone']); ?></span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Metas</h2>
                <?php if (count($goals) === 0) { ?>
                    <p class="muted">Nenhuma meta encontrada.</p>
                <?php } else { ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Titulo</th>
                                <th>Descricao</th>
                                <th>Data limite</th>
                                <th>Status</th>
                                <th>Progresso</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($goals as $goal) { ?>
                            <tr>
                                <td><?php echo safe_text($goal['titulo']); ?></td>
                                <td><?php echo nl2br(safe_text($goal['descricao'])); ?></td>
                                <td><?php echo safe_text(format_date($goal['data_limite'])); ?></td>
                                <td><?php echo safe_text(format_status($goal['status'])); ?></td>
                                <td><?php echo safe_text((string)(int)($goal['progresso'] ?? 0)); ?>%</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>

            <div class="section">
                <h2>Avaliacoes</h2>
                <?php if (count($evaluations) === 0) { ?>
                    <p class="muted">Nenhuma avaliacao encontrada.</p>
                <?php } else { ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Pontuacao</th>
                                <th>Avaliador</th>
                                <th>Status</th>
                                <th>Observacao</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($evaluations as $evaluation) { ?>
                            <tr>
                                <td><?php echo safe_text(format_date($evaluation['data'])); ?></td>
                                <td><?php echo safe_text($evaluation['tipo']); ?></td>
                                <td><?php echo safe_text((string)(int)($evaluation['pontuacao'] ?? 0)); ?></td>
                                <td><?php echo safe_text($evaluation['avaliador_nome']); ?></td>
                                <td><?php echo safe_text($evaluation['status'] ?? '-'); ?></td>
                                <td><?php echo nl2br(safe_text($evaluation['observacao'])); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>

            <div class="section">
                <h2>Feedbacks</h2>
                <?php if (count($feedbacks) === 0) { ?>
                    <p class="muted">Nenhum feedback encontrado.</p>
                <?php } else { ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Autor</th>
                                <th>Conteudo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($feedbacks as $feedback) { ?>
                            <tr>
                                <td><?php echo safe_text(format_date($feedback['data_feedback'])); ?></td>
                                <td><?php echo safe_text($feedback['tipo']); ?></td>
                                <td><?php echo safe_text($feedback['autor_exibicao']); ?></td>
                                <td><?php echo nl2br(safe_text($feedback['conteudo'])); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>
