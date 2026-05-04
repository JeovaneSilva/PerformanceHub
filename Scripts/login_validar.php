<?php

include_once '../inc/funcoes.php';
require_once '../inc/conexao.php';

$retorno = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vLogin = $_POST["pLogin"] ?? '';
    $vSenha = $_POST["pSenha"] ?? '';

    $vSenhaHash = sha1($vSenha);

    $sql = "
        SELECT senha 
        FROM tbUsuarios 
        WHERE login = ? 
        LIMIT 1
    ";

    $sql_login = $conexao->prepare($sql);
    $sql_login->bind_param("s", $vLogin);
    $sql_login->execute();
    $sql_login->bind_result($senhaBanco);

    if ($sql_login->fetch()) {
        if ($vSenhaHash === $senhaBanco) {
            $retorno = 1; 
            
            $_SESSION['logado'] = true;
            $_SESSION['usuario_login'] = $vLogin;
        }
    }

    $sql_login->close();
    $conexao->close();

    echo $retorno;
} else {
    echo 0;
}
?>