<?php
session_start();
require_once '../../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT * FROM inscricoes WHERE status = 'ativa' ORDER BY data_inscricao DESC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $registrations = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'registrations' => $registrations
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
