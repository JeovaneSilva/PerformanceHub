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

    $data = trim($_POST['pData'] ?? '');
    $observacao = trim($_POST['pObservacao'] ?? '');
    $funcionario_id = trim($_POST['pFuncionarioId'] ?? '');
    $avaliacao_status_id = trim($_POST['pAvaliacaoStatusId'] ?? '');
    $tipo = trim($_POST['pTipo'] ?? '');
    $pontuacao = trim($_POST['pPontuacao'] ?? '');
    $avaliador_nome = trim($_POST['pAvaliadorNome'] ?? '');

    if ($data == '' || $funcionario_id == '' || $avaliacao_status_id == '' || $tipo == '' || $pontuacao == '' || $avaliador_nome == '') {
        throw new Exception("Preencha os campos obrigatorios.");
    }
    if (!is_numeric($funcionario_id) || !is_numeric($avaliacao_status_id)) {
        throw new Exception("Funcionario ou status invalido.");
    }

    $pontuacao_int = is_numeric($pontuacao) ? (int)$pontuacao : 0;
    if ($pontuacao_int < 0) $pontuacao_int = 0;
    if ($pontuacao_int > 100) $pontuacao_int = 100;

    date_default_timezone_set('America/Fortaleza');
    $data_atual = date("Y-m-d H:i:s");
    $atualizado_por = $_SESSION['usuario_id'] ?? 1;

    $sql = "
        INSERT INTO tbAvaliacao (data, atualizado_em, observacao, funcionario_id, avaliacao_status_id, atualizado_por, tipo, pontuacao, avaliador_nome)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no prepare: " . $conexao->error);
    }

    $funcionario_id_int = (int)$funcionario_id;
    $avaliacao_status_id_int = (int)$avaliacao_status_id;
    $atualizado_por_int = (int)$atualizado_por;

    $stmt->bind_param(
        "sssiiisis",
        $data,
        $data_atual,
        $observacao,
        $funcionario_id_int,
        $avaliacao_status_id_int,
        $atualizado_por_int,
        $tipo,
        $pontuacao_int,
        $avaliador_nome
    );

    if ($stmt->execute()) {
        $retorno = [
            'status' => 'success',
            'message' => 'Avaliacao cadastrada com sucesso!',
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
