<?php
// Tenta carregar o seu arquivo de conexão
require_once 'conexao.php';

// Se o código chegar até esta linha sem "morrer" (die) lá no conexao.php, 
// significa que a conexão foi um sucesso absoluto!
echo "<h1 style='color: green;'>✅ Conexão realizada com sucesso!</h1>";
echo "<p>O PHP conseguiu se conectar ao banco de dados: <strong>" . $banco . "</strong></p>";

// Opcional: Mostra as informações do servidor via MySQLi
echo "<p>Informações do Host: " . $conexao->host_info . "</p>";

// Fecha a conexão após o teste
$conexao->close();
?>