<?php

include_once '../inc/funcoes.php';
require_once '../inc/conexao.php';

$retorno = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vLogin = $_POST["pLogin"] ?? '';
    $vSenha = $_POST["pSenha"] ?? '';

    if (empty(trim($vLogin)) || empty(trim($vSenha))) {
        echo 0; 
        exit; 
    }

    $vSenhaHash = sha1($vSenha);

    $sql = "
        SELECT usuario_id, senha 
        FROM tbUsuarios 
        WHERE login = ? 
        LIMIT 1
    ";

    $sql_login = $conexao->prepare($sql);
    $sql_login->bind_param("s", $vLogin);
    $sql_login->execute();
    
    $sql_login->bind_result($vIdBanco, $senhaBanco);

    if ($sql_login->fetch()) {
        if ($vSenhaHash === $senhaBanco) {
            $retorno = 1; 
            
            $_SESSION['logado'] = true;
            $_SESSION['usuario_login'] = $vLogin;
            $_SESSION['usuario_id'] = $vIdBanco;
        }
    }

    $sql_login->close();
    $conexao->close();

    echo $retorno;
} else {
    echo 0;
}
?>