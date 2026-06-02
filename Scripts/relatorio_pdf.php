<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    die("Acesso negado");
}

require_once '../inc/conexao.php';
require_once '../inc/fpdf.php';

function ajustaAcentos($texto) {
    if (empty($texto)) return '';
    return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
}

function formataData($data) {
    if (empty($data) || $data == '0000-00-00' || $data == '0000-00-00 00:00:00') return '-';
    return date('d/m/Y', strtotime($data));
}

$pessoa_id = $_GET['id'] ?? 0;

if (!is_numeric($pessoa_id) || $pessoa_id <= 0) {
    die("ID de funcionário inválido.");
}


$sql_pessoa = "SELECT nome, email, cpf, nascimento, telefone, cargo, departamento, data_admissao, performance_score 
               FROM tbPessoas WHERE pessoa_id = ?";
$stmt_pessoa = $conexao->prepare($sql_pessoa);
$stmt_pessoa->bind_param("i", $pessoa_id);
$stmt_pessoa->execute();
$result_pessoa = $stmt_pessoa->get_result();

if ($result_pessoa->num_rows === 0) {
    die("Funcionário não encontrado.");
}
$func = $result_pessoa->fetch_assoc();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);

$corFundoTitulo = [30, 58, 138]; 
$corTextoTitulo = [255, 255, 255];

$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(30, 58, 138);
$pdf->Cell(0, 10, ajustaAcentos('Relatório Completo de Desempenho'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 6, ajustaAcentos('PerformanceHub - Gerado em: ' . date('d/m/Y \à\s H:i')), 0, 1, 'C');
$pdf->Ln(10);


$pdf->SetFillColor($corFundoTitulo[0], $corFundoTitulo[1], $corFundoTitulo[2]);
$pdf->SetTextColor($corTextoTitulo[0], $corTextoTitulo[1], $corTextoTitulo[2]);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, ajustaAcentos('  1. DADOS CADASTRAIS E PROFISSIONAIS'), 0, 1, 'L', true);
$pdf->Ln(3);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(20, 7, 'Nome:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 7, ajustaAcentos($func['nome']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 7, 'CPF:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, ajustaAcentos($func['cpf']), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 7, 'E-mail:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 7, ajustaAcentos($func['email']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 7, 'Telefone:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, ajustaAcentos($func['telefone']), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 7, 'Nascimento:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(85, 7, formataData($func['nascimento']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(22, 7, ajustaAcentos('Admissão:'), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, formataData($func['data_admissao']), 0, 1);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 7, 'Cargo:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(65, 7, ajustaAcentos($func['cargo']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(28, 7, 'Departamento:', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 7, ajustaAcentos($func['departamento']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 7, 'Score Atual:', 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(16, 185, 129);
$pdf->Cell(0, 7, $func['performance_score'], 0, 1);

$pdf->Ln(8);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFillColor($corFundoTitulo[0], $corFundoTitulo[1], $corFundoTitulo[2]);
$pdf->SetTextColor($corTextoTitulo[0], $corTextoTitulo[1], $corTextoTitulo[2]);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, ajustaAcentos('  2. HISTÓRICO DE METAS'), 0, 1, 'L', true);
$pdf->Ln(3);

$sql_metas = "SELECT titulo, descricao, data_limite, status, progresso FROM tbMetas WHERE funcionario_id = ? ORDER BY data_limite DESC";
$stmt_metas = $conexao->prepare($sql_metas);
$stmt_metas->bind_param("i", $pessoa_id);
$stmt_metas->execute();
$result_metas = $stmt_metas->get_result();

$pdf->SetTextColor(0, 0, 0);
if ($result_metas->num_rows > 0) {
    while ($meta = $result_metas->fetch_assoc()) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(130, 6, ajustaAcentos('Meta: ' . $meta['titulo']), 0, 0);
        
        $pdf->SetFont('Arial', 'B', 9);
        $status_texto = strtoupper($meta['status']);
        $pdf->Cell(50, 6, ajustaAcentos('Status: ' . $status_texto . ' (' . $meta['progresso'] . '%)'), 0, 1);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, ajustaAcentos('Data Limite: ' . formataData($meta['data_limite'])), 0, 1);
        
        $pdf->SetTextColor(80, 80, 80);
        $pdf->MultiCell(0, 5, ajustaAcentos('Descrição: ' . $meta['descricao']), 0, 'J');
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->Ln(3);
        $pdf->Cell(0, 0, '', 'T', 1, 'C');
        $pdf->Ln(3);
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 8, 'Nenhuma meta registrada para este funcionário.', 0, 1);
}
$pdf->Ln(5);

$pdf->SetFillColor($corFundoTitulo[0], $corFundoTitulo[1], $corFundoTitulo[2]);
$pdf->SetTextColor($corTextoTitulo[0], $corTextoTitulo[1], $corTextoTitulo[2]);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, ajustaAcentos('  3. AVALIAÇÕES DE DESEMPENHO'), 0, 1, 'L', true);
$pdf->Ln(3);

$sql_av = "SELECT data, tipo, pontuacao, avaliador_nome, observacao FROM tbAvaliacao WHERE funcionario_id = ? ORDER BY data DESC";
$stmt_av = $conexao->prepare($sql_av);
$stmt_av->bind_param("i", $pessoa_id);
$stmt_av->execute();
$result_av = $stmt_av->get_result();

$pdf->SetTextColor(0, 0, 0);
if ($result_av->num_rows > 0) {
    while ($av = $result_av->fetch_assoc()) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 6, 'Data: ' . formataData($av['data']), 0, 0);
        $pdf->Cell(60, 6, 'Tipo: ' . ucfirst(ajustaAcentos($av['tipo'])), 0, 0);
        $pdf->Cell(0, 6, 'Nota: ' . $av['pontuacao'], 0, 1);
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(0, 6, ajustaAcentos('Avaliador: ' . $av['avaliador_nome']), 0, 1);
        
        $pdf->SetTextColor(80, 80, 80);
        $pdf->MultiCell(0, 5, ajustaAcentos('Observação: ' . $av['observacao']), 0, 'J');
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->Ln(3);
        $pdf->Cell(0, 0, '', 'T', 1, 'C');
        $pdf->Ln(3);
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 8, ajustaAcentos('Nenhuma avaliação registrada.'), 0, 1);
}
$pdf->Ln(5);

$pdf->SetFillColor($corFundoTitulo[0], $corFundoTitulo[1], $corFundoTitulo[2]);
$pdf->SetTextColor($corTextoTitulo[0], $corTextoTitulo[1], $corTextoTitulo[2]);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, ajustaAcentos('  4. FEEDBACKS RECEBIDOS'), 0, 1, 'L', true);
$pdf->Ln(3);

$sql_feed = "SELECT data_feedback, tipo, autor_nome, conteudo FROM tbFeedbacks WHERE funcionario_id = ? ORDER BY data_feedback DESC";
$stmt_feed = $conexao->prepare($sql_feed);
$stmt_feed->bind_param("i", $pessoa_id);
$stmt_feed->execute();
$result_feed = $stmt_feed->get_result();

$pdf->SetTextColor(0, 0, 0);
if ($result_feed->num_rows > 0) {
    while ($feed = $result_feed->fetch_assoc()) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 6, 'Data: ' . formataData($feed['data_feedback']), 0, 0);
        $pdf->Cell(60, 6, 'Tipo: ' . ucfirst(ajustaAcentos($feed['tipo'])), 0, 0);
        $pdf->Cell(0, 6, 'Autor: ' . ajustaAcentos($feed['autor_nome']), 0, 1);
        
        $pdf->SetFont('Arial', 'I', 9);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->MultiCell(0, 5, ajustaAcentos('"' . $feed['conteudo'] . '"'), 0, 'J');
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->Ln(3);
        $pdf->Cell(0, 0, '', 'T', 1, 'C');
        $pdf->Ln(3);
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 8, 'Nenhum feedback registrado.', 0, 1);
}


$stmt_pessoa->close();
$stmt_metas->close();
$stmt_av->close();
$stmt_feed->close();
$conexao->close();

$nome_arquivo = preg_replace('/[^A-Za-z0-9\-]/', '_', strtolower($func['nome']));
$pdf->Output('D', 'Dossie_RH_' . $nome_arquivo . '.pdf');
?>