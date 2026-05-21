<?php
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

    $ids = $_POST['pIds'] ?? [];

    if (empty($ids) || !is_array($ids)) {
        throw new Exception("Nenhum usuário selecionado.");
    }

    $sql = "DELETE FROM tbUsuarios WHERE usuario_id = ?";
    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no prepare: " . $conexao->error);
    }

    $sucesso_count = 0;

    foreach ($ids as $id) {
        $id_clean = trim($id);
        if (is_numeric($id_clean)) {
            $stmt->bind_param("i", $id_clean);
            if ($stmt->execute()) {
                $sucesso_count++;
            }
        }
    }

    if ($sucesso_count > 0) {
        $retorno = [
            'status' => 'success',
            'message' => $sucesso_count . ' usuário(s) excluído(s) com sucesso.'
        ];
    } else {
        throw new Exception("Não foi possível excluir os registros.");
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