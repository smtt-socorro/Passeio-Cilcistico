<?php
require_once 'config/functions.php';

$page_title = "Verificar Inscrição";
$admin_area = false;

// Processar requisição AJAX
if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    header('Content-Type: application/json');
    
    try {
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
        
        if (!validarCPF($cpf)) {
            echo json_encode(['success' => false, 'message' => 'CPF inválido']);
            exit();
        }
        
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT * FROM inscricoes WHERE cpf = :cpf AND status = 'ativa' ORDER BY data_inscricao DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();
        
        $inscricoes = $stmt->fetchAll();
        
        if (count($inscricoes) > 0) {
            echo json_encode(['success' => true, 'inscricoes' => $inscricoes]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Nenhuma inscrição encontrada para este CPF']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
    }
    
    exit();
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/verificar.css">

<!-- Página de Verificação -->
<section class="verification-page">
    <div class="verification-hero">
        <div class="verification-container">
            <!-- Header Get in Touch - Baseado nas imagens -->
            <div class="verification-header">
                <!--<p class="verification-subtitle">Get in Touch</p>-->
                <h1 class="verification-title">Consulte sua Inscrição</h1>
                <!--<p class="verification-description">-->
                    Digite seu CPF para consultar suas inscrições no evento de ciclismo. 
                    Mantenha seus dados seguros - consulta apenas por CPF.
                </p>
            </div>

            <!-- Layout em duas colunas -->
            <div class="verification-layout">
                
                <!-- Coluna do Formulário de Busca -->
                <div class="search-column">
                    <form id="searchForm" class="search-form">
                        <div class="form-group">
                            <label for="cpf">
                                <i class="fas fa-id-card"></i>
                                CPF do Participante
                            </label>
                            <input type="text" id="cpf" name="cpf" 
                                   placeholder="000.000.000-00" required maxlength="14">
                            <div class="field-error"></div>
                        </div>

                        <button type="submit" class="search-button" id="searchButton">
                            <i class="fas fa-search"></i>
                            Consultar Inscrições
                        </button>
                        
                        <div style="background: #e8f2ff; padding: 15px; border-radius: 12px; margin-top: 20px;">
                            <p style="margin: 0; color: #1a202c; font-size: 0.95rem; text-align: center;">
                                <i class="fas fa-shield-alt" style="color: #4a90e2;"></i>
                                <strong>Privacidade:</strong> Por questões de segurança, a consulta é feita apenas por CPF
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Coluna de Informações - Baseada nas imagens -->
                <div class="info-column">
                    <div class="contact-info-card">
                        
                        <div class="contact-item">
                            <div class="contact-icon email-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email</h4>
                                <p>smtt@socorro.se.gov.br</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon phone-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Phone</h4>
                                <p>(79) 3259-3920</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon address-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Address</h4>
                                <p>Orla de Nossa Senhora do Socorro - SE</p>
                            </div>
                        </div>

                        <!-- Informações do Evento -->
                        <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid rgba(0, 0, 0, 0.08);">
                            <h4 style="color: #4a90e2; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-calendar-alt"></i> Informações do Evento
                            </h4>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li style="color: #1a202c; margin-bottom: 8px; padding-left: 20px; position: relative;">
                                    <span style="position: absolute; left: 0; color: #4a90e2;">•</span>
                                    <strong>Data:</strong> 29 de Agosto de 2025
                                </li>
                                <li style="color: #1a202c; margin-bottom: 8px; padding-left: 20px; position: relative;">
                                    <span style="position: absolute; left: 0; color: #4a90e2;">•</span>
                                    <strong>Horário:</strong> Concentração às 07:00h
                                </li>
                                <li style="color: #1a202c; margin-bottom: 8px; padding-left: 20px; position: relative;">
                                    <span style="position: absolute; left: 0; color: #4a90e2;">•</span>
                                    <strong>Local:</strong> Orla de Nossa Senhora do Socorro - SE
                                </li>
                                <li style="color: #1a202c; margin-bottom: 8px; padding-left: 20px; position: relative;">
                                    <span style="position: absolute; left: 0; color: #4a90e2;">•</span>
                                    <strong>Material:</strong> Retirar na SMTT com CPF e ID
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/verificar.js"></script>

<?php include 'includes/footer.php'; ?>
