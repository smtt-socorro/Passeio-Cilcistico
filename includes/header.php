<?php
// Determinar página atual para navegação ativa
$current_page = basename($_SERVER['PHP_SELF']);
$admin_area = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Evento de Bicicleta - SMTT Socorro. Inscreva-se para participar do maior evento de ciclismo da região em 16/08/2026.">
    <meta name="keywords" content="evento, bicicleta, ciclismo, socorro, sergipe, smtt">
    <meta name="author" content="SMTT Socorro">
    
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Pedala Socorro 2026</title>
    
    <link rel="stylesheet" href="<?php echo $admin_area ? '../' : ''; ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $admin_area ? '../' : ''; ?>assets/images/favicon.ico">
    
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <?php if (!$admin_area): ?>
        <!-- Header Público -->
        <header class="header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-bicycle"></i>
                </div>
                <span>Pedala Socorro 2026</span>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Início</a></li>
                    <li><a href="inscricao.php" class="<?php echo $current_page === 'inscricao.php' ? 'active' : ''; ?>">Inscrever-se</a></li>
                    <li><a href="verificar_inscricao.php" class="<?php echo $current_page === 'verificar_inscricao.php' ? 'active' : ''; ?>">Consultar</a></li>
                    <li><a href="faq.php" class="<?php echo $current_page === 'faq.php' ? 'active' : ''; ?>">FAQ</a></li>
                    <li><a href="termos.php" class="<?php echo $current_page === 'termos.php' ? 'active' : ''; ?>">Termos</a></li>
                    <li><a href="admin/login.php" style="opacity: 0.7;">Admin</a></li>
                </ul>
            </nav>
        </header>
        <?php endif; ?>
