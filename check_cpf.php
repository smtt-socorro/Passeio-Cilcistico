<?php
require_once 'config/functions.php';

header('Content-Type: application/json');

if ($_POST && isset($_POST['cpf'])) {
    try {
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
        
        if (!validarCPF($cpf)) {
            echo json_encode(['exists' => false, 'valid' => false]);
            exit();
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT COUNT(*) as total, STRING_AGG(id_inscricao_formatado, ',') as ids 
                  FROM inscricoes 
                  WHERE cpf = :cpf AND status = 'ativa'";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            echo json_encode([
                'exists' => true,
                'count' => (int)$result['total'],
                'ids' => explode(',', $result['ids']),
                'valid' => true
            ]);
        } else {
            echo json_encode([
                'exists' => false,
                'count' => 0,
                'valid' => true
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'CPF não fornecido']);
}
?>
