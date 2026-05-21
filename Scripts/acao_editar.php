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
        throw new Exception("Método inválido");
    }

    $id = trim($_POST['pId'] ?? '');
    $nome = trim($_POST['pNome'] ?? '');
    $login = trim($_POST['pLogin'] ?? ''); 
    $senha = trim($_POST['pSenha'] ?? '');

    if ($id == "" || $nome == "" || $login == "") {
        throw new Exception("Preencha todos os campos obrigatórios.");
    }
    if (!is_numeric($id)) {
        throw new Exception("ID do usuário inválido.");
    }

    date_default_timezone_set('America/Fortaleza'); 
    $data_atual = date("Y-m-d H:i:s");

    $atualizado_por = $_SESSION['usuario_id'];

    if ($senha != "") {
        $senhaHash = sha1($senha);
        $sql = "UPDATE tbUsuarios SET nome = ?, login = ?, senha = ?, atualizado_em = ?, atualizado_por = ? WHERE usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        if (!$stmt) throw new Exception("Erro no prepare: " . $conexao->error);
        
        $stmt->bind_param("ssssii", $nome, $login, $senhaHash, $data_atual, $atualizado_por, $id);
    } 
    else {
        $sql = "UPDATE tbUsuarios SET nome = ?, login = ?, atualizado_em = ?, atualizado_por = ? WHERE usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        if (!$stmt) throw new Exception("Erro no prepare: " . $conexao->error);
        
        $stmt->bind_param("sssii", $nome, $login, $data_atual, $atualizado_por, $id);
    }

    if (!$stmt->execute()) {
        throw new Exception("Erro ao atualizar usuário: " . $stmt->error);
    }

    if ($stmt->affected_rows >= 0) {
        $retorno = ['status' => 'success', 'message' => 'Usuário atualizado com sucesso.'];
    } else {
        $retorno = ['status' => 'error', 'message' => 'Nenhum registro foi atualizado.'];
    }

    $stmt->close();
    $conexao->close();

} catch (Exception $e) {
    $retorno = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($retorno);
exit;
?>