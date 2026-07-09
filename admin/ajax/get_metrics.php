<?php
session_start();
require_once '../../config/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->query("SELECT COUNT(*) AS total FROM inscricoes WHERE status = 'ativa'");
    $totalInscricoes = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->query("SELECT COUNT(*) AS hoje FROM inscricoes WHERE data_inscricao::date = CURRENT_DATE AND status = 'ativa'");
    $inscricoesHoje = (int) $stmt->fetch(PDO::FETCH_ASSOC)['hoje'];

    $stmt = $conn->query("
        SELECT COUNT(DISTINCT bairro) AS total
        FROM inscricoes
        WHERE status = 'ativa'
          AND bairro IS NOT NULL
          AND TRIM(bairro) <> ''
    ");
    $bairrosCadastrados = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $conn->query("
        SELECT COALESCE(ROUND(AVG(EXTRACT(YEAR FROM AGE(CURRENT_DATE, data_nascimento)))), 0) AS media_idade
        FROM inscricoes
        WHERE status = 'ativa'
          AND data_nascimento IS NOT NULL
    ");
    $mediaIdade = (int) $stmt->fetch(PDO::FETCH_ASSOC)['media_idade'];

    echo json_encode([
        'success' => true,
        'metrics' => [
            'total_inscricoes' => $totalInscricoes,
            'inscricoes_hoje' => $inscricoesHoje,
            'bairros_populares' => $bairrosCadastrados,
            'media_idade' => $mediaIdade,

            'total_change' => 0,
            'hoje_change' => 0,
            'bairros_change' => 0,
            'idade_change' => 0
        ]
    ]);
} catch (Throwable $e) {
    error_log('Erro em get_metrics.php: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Erro ao carregar métricas'
    ]);
}