<?php
include_once '../inc/funcoes.php';
require_once '../inc/conexao.php';

$retorno = 0; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $vNome = $_POST["pNome"] ?? '';
    $vEmail = $_POST["pEmail"] ?? '';
    $vSenha = $_POST["pSenha"] ?? '';

    if (empty(trim($vNome)) || empty(trim($vEmail)) || empty(trim($vSenha))) {
        echo 0;
        exit;
    }

    $sql_verifica = "SELECT usuario_id FROM tbUsuarios WHERE login = ? LIMIT 1";
    $stmt_verifica = $conexao->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $vEmail);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        echo 2;
        $stmt_verifica->close();
        $conexao->close();
        exit;
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
    
    $stmt_insert->bind_param("ssssi", $vNome, $vEmail, $vSenhaHash, $data_atual, $atualizado_por);

    if ($stmt_insert->execute()) {
        $retorno = 1;
    }

    $stmt_insert->close();
    $conexao->close();

    echo $retorno;
} else {
    echo 0;
}
?>