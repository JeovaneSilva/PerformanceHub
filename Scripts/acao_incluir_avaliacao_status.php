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

    $descricao = trim($_POST['pDescricao'] ?? '');

    if ($descricao == '') {
        throw new Exception("Preencha a descricao.");
    }

    $sql = "INSERT INTO tbAvaliacaoStatus (descricao) VALUES (?)";
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no prepare: " . $conexao->error);
    }

    $stmt->bind_param("s", $descricao);

    if ($stmt->execute()) {
        $retorno = [
            'status' => 'success',
            'message' => 'Status cadastrado com sucesso!',
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
