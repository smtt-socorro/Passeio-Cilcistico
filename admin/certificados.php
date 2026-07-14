<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit();
}

$page_title = "Imprimir Cartão de Inscrição";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Socorro no Pedal 2026</title>
    
    <!-- CSS Globais e do Admin -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin-index.css">
    
    <!-- CSS Específico da Página de Certificados -->
    <link rel="stylesheet" href="assets/css/certificados.css"> 
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body>

<div class="admin-dashboard">
    <!-- Header Admin -->
    <header class="admin-header">
        <nav class="admin-nav">
            <a href="index.php" class="admin-logo">
                <i class="fas fa-bicycle"></i>
                Admin - Socorro no Pedal 2026
            </a>
            <div class="admin-user-menu">
                <div class="admin-user-info">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr(htmlspecialchars($_SESSION['admin_nome'] ?? 'A'), 0, 1)); ?>
                    </div>
                    <div class="admin-user-details">
                        <strong><?php echo htmlspecialchars($_SESSION['admin_nome'] ?? 'Administrador'); ?></strong>
                        <span>Administrador do Sistema</span>
                    </div>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </nav>
    </header>

    <!-- Container Principal -->
    <div class="admin-container">
        
        <!-- Header da Página -->
        <div class="page-header">
            <div class="page-title-section">
                <h1 class="page-title"><?php echo htmlspecialchars($page_title); ?></h1>
                <p class="page-subtitle">Busque pelo participante e imprima o cartão de inscrição do evento.</p>
            </div>
            <div class="page-actions">
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Voltar ao Dashboard
                </a>
            </div>
        </div>

        <!-- Área de Busca -->
        <div class="search-area">
            <div class="search-form-container">
                <label for="buscaParticipante">Buscar Participante (Nome ou ID da Inscrição):</label>
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon-input"></i>
                    <input type="text" id="buscaParticipante" placeholder="Digite para buscar...">
                </div>
                <div id="suggestionsList" class="suggestions-list"></div>
            </div>
        </div>

        <!-- Área de Pré-visualização e Impressão do Cartão -->
        <div id="certificateActionArea" class="certificate-action-area" style="display:none;">
            <div class="certificate-preview-wrapper">
                <h3>Pré-visualização do Cartão de Inscrição:</h3>
                <!-- Estrutura do Cartão SIMPLIFICADA -->
                <div id="certificateContent" class="certificate-a4-horizontal card-design">
                    
                    <div class="card-main-content">
                        <!-- REMOVIDO: Nome do participante -->
                        <div class="card-registration-id">
                            <span class="id-prefix" id="cardIdPrefix">S</span><span class="id-number" id="cardIdNumber">0000</span>
                        </div>
                        <!-- REMOVIDO: Detalhes do evento -->
                    </div>

                    <div class="card-footer">
                        <img src="../assets/images/SMTT.png" alt="Logo SMTT Socorro" class="footer-logo" id="logo1">
                        <img src="../assets/images/PREFEITURAMUNICIPAL.png" alt="Logo Governo Municipal Socorro" class="footer-logo" id="logo2">
                        <img src="../assets/images/SEMOB.png" alt="Logo SEMOB" class="footer-logo" id="logo3">
                    </div>
                </div>
            </div>
            <button id="btnPrintCertificate" class="btn-primary btn-print-lg">
                <i class="fas fa-print"></i> Imprimir Cartão de Inscrição
            </button>
        </div>
        
        <div id="noParticipantSelected" class="empty-state certificate-empty">
            <i class="fas fa-user-search"></i>
            <h3>Nenhum participante selecionado</h3>
            <p>Utilize a busca acima para encontrar um participante e gerar o cartão.</p>
        </div>

    </div>
</div>

<!-- JavaScript Específico da Página -->
<script src="assets/js/certificados.js"></script>

</body>
</html>
