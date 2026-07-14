<?php
$page_title = "início";
$admin_area = false;
include 'includes/header.php';
?>

<link
    rel="stylesheet"
    href="assets/css/index.css?v=<?= filemtime(__DIR__ . '/assets/css/index.css') ?>"
>

<div class="index-page">
    <!-- Hero Section Minimal -->
    <section class="hero-minimal">
        <!-- Elementos decorativos -->
        <div class="hero-decoration">
            <div class="decoration-circle"></div>
            <div class="decoration-circle"></div>
            <div class="decoration-circle"></div>
        </div>

        <div class="hero-container">
            <!-- Badge do evento -->
            <div class="event-badge">
                <div class="event-badge-icon">
                    <i class="fas fa-bicycle"></i>
                </div>
                Passeio ciclistico 2026
            </div>

            <!-- Título principal -->
            <h1 class="hero-title">
                Passeio Ciclistico<br>
                <span class="hero-title-accent">SMTT</span>
            </h1>

            <!-- Subtítulo -->
            <p class="hero-subtitle">
                Participe do maior evento de ciclismo da região e viva uma experiência inesquecível nas belas paisagens de Socorro
            </p>

            <!-- Informações do evento -->
            <div class="event-info-grid">
                <div class="event-info-card">
                    <div class="event-info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="event-info-title">Data do Evento</div>
                    <div class="event-info-text">16 de Agosto de 2026<br>Concentração às 06:00h</div>
                </div>

                <div class="event-info-card">
                    <div class="event-info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="event-info-title">Local de Concentração</div>
                    <div class="event-info-text">Na Praça Eu Amo Socorro - SE</div>

                    <a
                        href="https://www.google.com/maps/search/?api=1&query=Av.%20Ruy%20de%20Gomes%20Menezes%20-%20Marcos%20Freire%20II%2C%20Nossa%20Senhora%20do%20Socorro%20-%20SE"
                        class="map-text-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <i class="fas fa-map-marked-alt"></i>
                        Ver no Mapa
                    </a>
                </div>

                <div class="event-info-card">
                    <div class="event-info-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="event-info-title">Participação</div>
                    <div class="event-info-text">Máximo 1 por CPF<br>Participação mediante 2 kg de alimento</div>
                </div>
            </div>

            <!-- Contador regressivo -->
            <div class="countdown-section">
                <h3 class="countdown-title">Faltam apenas</h3>
                <div class="countdown-grid">
                    <div class="countdown-item" data-countdown="days">
                        <span class="countdown-number">00</span>
                        <span class="countdown-label">Dias</span>
                    </div>
                    <div class="countdown-item" data-countdown="hours">
                        <span class="countdown-number">00</span>
                        <span class="countdown-label">Horas</span>
                    </div>
                    <div class="countdown-item" data-countdown="minutes">
                        <span class="countdown-number">00</span>
                        <span class="countdown-label">Min</span>
                    </div>
                    <div class="countdown-item" data-countdown="seconds">
                        <span class="countdown-number">00</span>
                        <span class="countdown-label">Seg</span>
                    </div>
                </div>
            </div>

            <!-- Botões de ação -->
            <div class="hero-actions">
                <a href="inscricao.php" class="btn-primary">
                    <i class="fas fa-bicycle"></i>
                    Fazer Inscrição
                </a>
                <a href="verificar_inscricao.php" class="btn-secondary">
                    <i class="fas fa-search"></i>
                    Consultar Inscrição
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <!--<section class="features-minimal scroll-reveal">
        <div class="features-container">
            <div class="features-header">
                <h2 class="features-title">Por que participar?</h2>
                <p class="features-subtitle">
                    Uma experiência única de ciclismo com toda a segurança e organização que você merece
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Segurança Total</h3>
                    <p class="feature-description">
                        Evento organizado com todo apoio da SMTT, garantindo segurança e suporte durante todo o percurso
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3 class="feature-title">Trajeto Incrível</h3>
                    <p class="feature-description">
                        Percorra as mais belas paisagens de Socorro em um trajeto cuidadosamente planejado
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3 class="feature-title">Material Incluso</h3>
                    <p class="feature-description">
                        Receba kit completo do evento na SMTT apresentando seu CPF e ID de inscrição
                    </p>
                </div>
            </div>
        </div>
    </section>-->
</div>

<script src="assets/js/index.js"></script>

<?php include 'includes/footer.php'; ?>
