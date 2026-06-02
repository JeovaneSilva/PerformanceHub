<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
require_once '../inc/conexao.php';

$retorno = [
    'status' => 'error',
    'message' => 'Erro desconhecido'
];

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Metodo invalido");
    }

    $funcionario_id = trim($_POST['pFuncionarioId'] ?? '');
    $titulo = trim($_POST['pTitulo'] ?? '');
    $descricao = trim($_POST['pDescricao'] ?? '');
    $data_limite = trim($_POST['pDataLimite'] ?? '');
    $status = trim($_POST['pStatus'] ?? 'pendente');
    $progresso = trim($_POST['pProgresso'] ?? '0');

    if ($funcionario_id == '' || $titulo == '' || $data_limite == '') {
        throw new Exception("Preencha os campos obrigatorios.");
    }
    if (!is_numeric($funcionario_id)) {
        throw new Exception("Funcionario invalido.");
    }

    $progresso_int = is_numeric($progresso) ? (int)$progresso : 0;
    if ($progresso_int < 0) $progresso_int = 0;
    if ($progresso_int > 100) $progresso_int = 100;

    if ($status == '') {
        $status = 'pendente';
    }

    date_default_timezone_set('America/Fortaleza');
    $data_atual = date("Y-m-d H:i:s");
    $atualizado_por = $_SESSION['usuario_id'] ?? 1;

    $sql = "
        INSERT INTO tbMetas (funcionario_id, titulo, descricao, data_limite, status, progresso, atualizado_em, atualizado_por)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no prepare: " . $conexao->error);
    }

    $funcionario_id_int = (int)$funcionario_id;
    $atualizado_por_int = (int)$atualizado_por;

    $stmt->bind_param("issssisi", $funcionario_id_int, $titulo, $descricao, $data_limite, $status, $progresso_int, $data_atual, $atualizado_por_int);

    if ($stmt->execute()) {
        $retorno = [
            'status' => 'success',
            'message' => 'Meta cadastrada com sucesso!',
            'id' => $conexao->insert_id
        ];
    } else {
        throw new Exception("Erro ao gravar: " . $stmt->error);
    }

    $stmt->close();
    $conexao->close();

} catch (Throwable $e) {
    $retorno = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($retorno);
exit;
?>
