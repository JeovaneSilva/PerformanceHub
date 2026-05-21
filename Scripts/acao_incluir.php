<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../inc/funcoes.php';
require_once '../inc/conexao.php';

$retorno = [
    'status' => 'error',
    'message' => 'Erro desconhecido'
];

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Método inválido");
    }
    
    $vNome = trim($_POST["pNome"] ?? '');
    $vEmail = trim($_POST["pEmail"] ?? '');
    $vSenha = trim($_POST["pSenha"] ?? '');

    if (empty($vNome) || empty($vEmail) || empty($vSenha)) {
        throw new Exception("Preencha todos os campos");
    }

    $sql_verifica = "SELECT usuario_id FROM tbUsuarios WHERE login = ? LIMIT 1";
    $stmt_verifica = $conexao->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $vEmail);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        $stmt_verifica->close();
        throw new Exception("Este e-mail já está cadastrado!");
    }
    $stmt_verifica->close();

    $vSenhaHash = sha1($vSenha); 
    date_default_timezone_set('America/Fortaleza');
    $data_atual = date("Y-m-d H:i:s");
    $atualizado_por = 1; 

    $sql_insert = "
        INSERT INTO tbUsuarios (nome, login, senha, atualizado_em, atualizado_por) 
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt_insert = $conexao->prepare($sql_insert);
    if (!$stmt_insert) {
        throw new Exception("Erro no prepare: " . $conexao->error);
    }
    
    $stmt_insert->bind_param("ssssi", $vNome, $vEmail, $vSenhaHash, $data_atual, $atualizado_por);

    if ($stmt_insert->execute()) {
        $retorno = [
            'status' => 'success',
            'message' => 'Conta criada com sucesso!',
            'id' => $conexao->insert_id
        ];
    } else {
        throw new Exception("Erro ao gravar: " . $stmt_insert->error);
    }

    $stmt_insert->close();
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