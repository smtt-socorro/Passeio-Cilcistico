<?php
session_start();
require_once '../../config/functions.php';

header('Content-Type: application/json');

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado', 'participants' => []]);
    exit();
}

$term = $_POST['term'] ?? '';

if (empty($term) || strlen($term) < 2) {
    echo json_encode(['success' => false, 'message' => 'Termo de busca muito curto', 'participants' => []]);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $searchTerm = "%" . $term . "%";
    
    // Busca por nome_completo OU id_inscricao_formatado
    $query = "SELECT id, nome_completo, id_inscricao_formatado 
              FROM inscricoes 
              WHERE (nome_completo LIKE ? OR id_inscricao_formatado LIKE ?) 
              AND status = 'ativa' 
              LIMIT 10";
              
    $stmt = $conn->prepare($query);
    $stmt->execute([$searchTerm, $searchTerm]);
    $participants = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'participants' => $participants]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'participants' => []]);
}
?>
