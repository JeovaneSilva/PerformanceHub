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

    $nome = trim($_POST['pNome'] ?? '');
    $cpf = trim($_POST['pCpf'] ?? '');
    $nascimento = trim($_POST['pNascimento'] ?? '');
    $telefone = trim($_POST['pTelefone'] ?? '');
    $pessoa_tipo_id = trim($_POST['pPessoaTipoId'] ?? '');
    $email = trim($_POST['pEmail'] ?? '');
    $cargo = trim($_POST['pCargo'] ?? '');
    $departamento = trim($_POST['pDepartamento'] ?? '');
    $data_admissao = trim($_POST['pDataAdmissao'] ?? '');
    $performance_score = trim($_POST['pPerformanceScore'] ?? '0');

    if ($nome == '' || $pessoa_tipo_id == '' || $email == '' || $cargo == '' || $departamento == '' || $data_admissao == '') {
        throw new Exception("Preencha todos os campos obrigatorios.");
    }
    if (!is_numeric($pessoa_tipo_id)) {
        throw new Exception("Tipo de pessoa invalido.");
    }

    $performance_int = is_numeric($performance_score) ? (int)$performance_score : 0;
    if ($performance_int < 0) $performance_int = 0;
    if ($performance_int > 100) $performance_int = 100;

    date_default_timezone_set('America/Fortaleza');
    $data_atual = date("Y-m-d H:i:s");
    $atualizado_por = $_SESSION['usuario_id'] ?? 1;

    $sql = "
        INSERT INTO tbPessoas (nome, cpf, nascimento, telefone, pessoa_tipo_id, email, cargo, departamento, data_admissao, performance_score, atualizado_por, atualizado_em)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conexao->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro no prepare: " . $conexao->error);
    }

    $pessoa_tipo_id_int = (int)$pessoa_tipo_id;
    $atualizado_por_int = (int)$atualizado_por;

    $cpf_db = $cpf !== '' ? $cpf : null;
    $nascimento_db = $nascimento !== '' ? $nascimento : null;
    $telefone_db = $telefone !== '' ? $telefone : null;

    $stmt->bind_param(
        "ssssissssiis",
        $nome,
        $cpf_db,
        $nascimento_db,
        $telefone_db,
        $pessoa_tipo_id_int,
        $email,
        $cargo,
        $departamento,
        $data_admissao,
        $performance_int,
        $atualizado_por_int,
        $data_atual
    );

    if ($stmt->execute()) {
        $retorno = [
            'status' => 'success',
            'message' => 'Funcionario cadastrado com sucesso!',
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
