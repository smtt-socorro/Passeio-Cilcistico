<?php
session_start();
require_once '../../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

if ($_POST) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $action = $_POST['action'] ?? '';
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('ID não fornecido');
        }
        
        switch($action) {
            case 'update':
                $nome_completo = sanitize($_POST['nome_completo']);
                $email = sanitize($_POST['email']);
                $telefone = sanitize($_POST['telefone']);
                $sexo = sanitize($_POST['sexo']);
                $religiao = sanitize($_POST['religiao']);
                $data_nascimento = $_POST['data_nascimento'];
                $cep = preg_replace('/[^0-9]/', '', $_POST['cep']);
                $logradouro = sanitize($_POST['logradouro']);
                $numero = sanitize($_POST['numero']);
                $complemento = sanitize($_POST['complemento']);
                $bairro = sanitize($_POST['bairro']);
                $cidade = sanitize($_POST['cidade']);
                $estado = sanitize($_POST['estado']);
                $status = $_POST['status'];
                $link_trajeto_maps = sanitize($_POST['link_trajeto_maps']);
                
                // Validações básicas
                if (empty($nome_completo) || empty($email) || empty($telefone)) {
                    throw new Exception('Campos obrigatórios não preenchidos');
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Email inválido');
                }
                
                $query = "UPDATE inscricoes SET 
                            nome_completo = ?, 
                            email = ?, 
                            telefone = ?, 
                            sexo = ?, 
                            religiao = ?, 
                            data_nascimento = ?, 
                            cep = ?, 
                            logradouro = ?, 
                            numero = ?, 
                            complemento = ?, 
                            bairro = ?, 
                            cidade = ?, 
                            estado = ?, 
                            status = ?, 
                            link_trajeto_maps = ?
                          WHERE id = ?";
                
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    $nome_completo, $email, $telefone, $sexo, $religiao, $data_nascimento,
                    $cep, $logradouro, $numero, $complemento,
                    $bairro, $cidade, $estado, $status, $link_trajeto_maps, $id
                ]);
                
                // Log da atividade
                logAtividade('Inscrição atualizada', "ID: {$id}, Admin: {$_SESSION['admin_nome']}");
                
                echo json_encode(['success' => true, 'message' => 'Inscrição atualizada com sucesso']);
                break;
                
            case 'delete':
                $query = "UPDATE inscricoes SET status = 'cancelada' WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([$id]);
                
                // Log da atividade
                logAtividade('Inscrição excluída', "ID: {$id}, Admin: {$_SESSION['admin_nome']}");
                
                echo json_encode(['success' => true, 'message' => 'Inscrição excluída com sucesso']);
                break;
                
            default:
                throw new Exception('Ação não reconhecida');
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>
