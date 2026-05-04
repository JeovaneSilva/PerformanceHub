<?php

$servidor = "76.13.92.115"; 

// $usuario = "u199367788_Ct5qqTLne_jeovaneAdmin";
$usuario = "u199367788_Ct5qqTLne_jeovaneAdm";                  
// $senha = "T?z*eHQ!;|9d"; 
$senha = "#0212Jeovanevyvian";                     
$banco = "u199367788_Ct5qqTLne_performancehub";                      

$conexao = new mysqli($servidor, $usuario, $senha, $banco);

if ($conexao->connect_error) { 
    die("Erro ao conectar: " . $conexao->connect_error); 
}

$conexao->set_charset("utf8mb4"); 
?>