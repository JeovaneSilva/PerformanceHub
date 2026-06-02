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

    $ids = [];
    if (isset($_POST['pIds']) && is_array($_POST['pIds'])) {
        $ids = $_POST['pIds'];
    } elseif (isset($_POST['pId'])) {
        $ids = [$_POST['pId']];
    }

    if (empty($ids)) {
        throw new Exception("Nenhum funcionario selecionado.");
    }

    $sql = "DELETE FROM tbPessoas WHERE pessoa_id = ?";
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no prepare: " . $conexao->error);
    }

    $sucesso_count = 0;

    foreach ($ids as $id) {
        $id_clean = trim($id);
        if (is_numeric($id_clean)) {
            $id_int = (int)$id_clean;
            $stmt->bind_param("i", $id_int);
            if ($stmt->execute()) {
                $sucesso_count++;
            }
        }
    }

    if ($sucesso_count > 0) {
        $retorno = [
            'status' => 'success',
            'message' => $sucesso_count . ' funcionario(s) excluido(s) com sucesso.'
        ];
    } else {
        throw new Exception("Nao foi possivel excluir os registros.");
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
