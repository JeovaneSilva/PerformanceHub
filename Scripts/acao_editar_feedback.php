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

    $feedback_id = trim($_POST['pId'] ?? '');
    $funcionario_id = trim($_POST['pFuncionarioId'] ?? '');
    $autor_id = trim($_POST['pAutorId'] ?? '');
    $autor_nome = trim($_POST['pAutorNome'] ?? '');
    $tipo = trim($_POST['pTipo'] ?? '');
    $conteudo = trim($_POST['pConteudo'] ?? '');
    $data_feedback = trim($_POST['pDataFeedback'] ?? '');

    if ($feedback_id == '' || !is_numeric($feedback_id)) {
        throw new Exception("Feedback invalido.");
    }
    if ($funcionario_id == '' || $tipo == '' || $conteudo == '' || $data_feedback == '') {
        throw new Exception("Preencha os campos obrigatorios.");
    }
    if (!is_numeric($funcionario_id)) {
        throw new Exception("Funcionario invalido.");
    }
    if ($autor_id == '' && $autor_nome == '') {
        throw new Exception("Autor invalido.");
    }

    date_default_timezone_set('America/Fortaleza');
    $data_atual = date("Y-m-d H:i:s");
    $atualizado_por = $_SESSION['usuario_id'] ?? 1;

    $funcionario_id_int = (int)$funcionario_id;
    $atualizado_por_int = (int)$atualizado_por;
    $feedback_id_int = (int)$feedback_id;

    if ($autor_id !== '' && is_numeric($autor_id)) {
        $autor_id_int = (int)$autor_id;
        $sql = "
            UPDATE tbFeedbacks
            SET funcionario_id = ?, autor_id = ?, autor_nome = ?, tipo = ?, conteudo = ?, data_feedback = ?, atualizado_em = ?, atualizado_por = ?
            WHERE feedback_id = ?
        ";
        $stmt = $conexao->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro no prepare: " . $conexao->error);
        }
        $stmt->bind_param("iisssssii", $funcionario_id_int, $autor_id_int, $autor_nome, $tipo, $conteudo, $data_feedback, $data_atual, $atualizado_por_int, $feedback_id_int);
    } else {
        $sql = "
            UPDATE tbFeedbacks
            SET funcionario_id = ?, autor_id = NULL, autor_nome = ?, tipo = ?, conteudo = ?, data_feedback = ?, atualizado_em = ?, atualizado_por = ?
            WHERE feedback_id = ?
        ";
        $stmt = $conexao->prepare($sql);
        if (!$stmt) {
            throw new Exception("Erro no prepare: " . $conexao->error);
        }
        $stmt->bind_param("isssssii", $funcionario_id_int, $autor_nome, $tipo, $conteudo, $data_feedback, $data_atual, $atualizado_por_int, $feedback_id_int);
    }

    if ($stmt->execute()) {
        $retorno = [
            'status' => 'success',
            'message' => 'Feedback atualizado com sucesso!'
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
