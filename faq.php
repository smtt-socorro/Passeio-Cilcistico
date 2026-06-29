<?php
$page_title = "Perguntas Frequentes";
$admin_area = false;
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/faq.css">

<!-- Página FAQ -->
<section class="faq-page">
    <div class="faq-hero">
        <div class="faq-container">
            <!-- Header Get in Touch - Baseado nas imagens -->
            <div class="faq-header">
               <!--<p class="faq-subtitle">Get in Touch</p>-->
                <h1 class="faq-title">Perguntas Frequentes</h1>
                <p class="faq-description">
                    Encontre respostas para as dúvidas mais comuns sobre o evento de ciclismo. 
                    Se não encontrar o que procura, entre em contato conosco.
                </p>
            </div>

            <!-- Layout em duas colunas -->
            <div class="faq-layout">
                
                <!-- Coluna do FAQ -->
                <div class="faq-column">
                    <!-- Barra de busca -->
                    <div class="faq-search">
                        <div class="search-group">
                            <input type="text" id="faq-search" class="search-input" 
                                   placeholder="Digite sua pergunta ou palavra-chave...">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>

                    <!-- Categorias -->
                    <div class="faq-categories">
                        <button class="category-btn active" data-category="all">
                            <i class="fas fa-th-large"></i> Todas
                        </button>
                        <button class="category-btn" data-category="inscricao">
                            <i class="fas fa-user-plus"></i> Inscrição
                        </button>
                        <button class="category-btn" data-category="evento">
                            <i class="fas fa-calendar-alt"></i> Evento
                        </button>
                        <button class="category-btn" data-category="material">
                            <i class="fas fa-gift"></i> Material
                        </button>
                        <button class="category-btn" data-category="geral">
                            <i class="fas fa-question-circle"></i> Geral
                        </button>
                    </div>

                    <!-- Lista de FAQs -->
                    <div class="faq-list">
                        
                        <!-- FAQ 1 - Inscrição -->
                        <div class="faq-item" data-category="inscricao" id="faq-como-inscrever">
                            <button class="faq-question">
                                <span>Como posso me inscrever no evento?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Para se inscrever no evento, siga estes passos:</p>
                                    <ul>
                                        <li>Acesse a <a href="inscricao.php">página de inscrição</a></li>
                                        <li>Preencha todos os campos obrigatórios</li>
                                        <li>Aceite os termos de responsabilidade</li>
                                        <li>Clique em "Realizar Inscrição"</li>
                                    </ul>
                                    <p>Após a inscrição, você receberá um ID único que deve ser guardado para retirada do material.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 - Inscrição -->
                        <div class="faq-item" data-category="inscricao" id="faq-limite-inscricoes">
                            <button class="faq-question">
                                <span>Quantas inscrições posso fazer com o mesmo CPF?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Cada CPF pode ter no máximo <strong>1 inscrição ativa</strong> no evento.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 - Evento -->
                        <div class="faq-item" data-category="evento" id="faq-data-evento">
                            <button class="faq-question">
                                <span>Quando e onde será o evento?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p><strong>Data:</strong> 16 de Agosto de 2026</p>
                                    <p><strong>Horário:</strong> Concentração às 06:00h</p>
                                    <p><strong>Local:</strong> Na Praça Eu Amo Socorro - SE</p>
                                    <p>Recomendamos chegar com antecedência para organização e aquecimento.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 - Material -->
                        <div class="faq-item" data-category="material" id="faq-retirada-material">
                            <button class="faq-question">
                                <span>Como e onde retirar o material do evento?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p><strong>Local:</strong> SMTT de Nossa Senhora do Socorro - SE</p>
                                    <p><strong>Itens e Documentos necessários:</strong></p>
                                    <ul>
                                        <li>CPF (documento físico ou digital)</li>
                                        <li>ID de inscrição (fornecido após o cadastro)</li>
                                        <li>2 kg de alimentos</li>
                                    </ul>
                                    <p><strong>Horário de funcionamento:</strong> Segunda a sexta, das 8h às 14h</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 - Evento -->
                        <div class="faq-item" data-category="evento" id="faq-equipamentos">
                            <button class="faq-question">
                                <span>Quais equipamentos são obrigatórios?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p><strong>Obrigatórios:</strong></p>
                                    <ul>
                                        <li>Capacete de ciclismo (uso obrigatório)</li>
                                        <li>Bicicleta em boas condições</li>
                                    </ul>
                                    <p><strong>Recomendados:</strong></p>
                                    <ul>
                                        <li>Roupas adequadas para ciclismo</li>
                                        <li>Garrafa de água</li>
                                        <li>Protetor solar</li>
                                        <li>Kit básico de reparo</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 - Geral -->
                        <div class="faq-item" data-category="geral" id="faq-idade-minima">
                            <button class="faq-question">
                                <span>Existe idade mínima para participar?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>A idade mínima para participar é de <strong>10 anos</strong>.</p>
                                    <p>Menores de 18 anos devem ter autorização dos responsáveis e estar acompanhados durante o evento.</p>
                                    <p>Recomendamos que crianças tenham experiência prévia com ciclismo.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 7 - Inscricao -->
                        <div class="faq-item" data-category="inscricao" id="faq-alterar-dados">
                            <button class="faq-question">
                                <span>Posso alterar meus dados após a inscrição?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Sim, é possível alterar alguns dados após a inscrição.</p>
                                    <p><strong>Para alterações, entre em contato:</strong></p>
                                    <ul>
                                        <li>Email: smtt@socorro.se.gov.br</li>
                                        <li>Telefone: (79) 99898-1288</li>
                                    </ul>
                                    <p>Tenha em mãos seu ID de inscrição e CPF para facilitar o atendimento.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 8 - Evento -->
                        <div class="faq-item" data-category="evento" id="faq-chuva">
                            <button class="faq-question">
                                <span>O evento acontece mesmo se chover?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>O evento será realizado independente das condições climáticas, exceto em casos de <strong>chuva forte ou tempestade</strong>.</p>
                                    <p>Em caso de adiamento por motivos climáticos, os participantes serão comunicados através dos canais oficiais com pelo menos 2 horas de antecedência.</p>
                                    <p>Recomendamos verificar a previsão do tempo e trazer equipamentos adequados.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 9 - Geral -->
                        <div class="faq-item" data-category="geral" id="faq-cancelar-inscricao">
                            <button class="faq-question">
                                <span>Como cancelar minha inscrição?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>Para cancelar sua inscrição, entre em contato conosco:</p>
                                    <ul>
                                        <li>Email: smtt@socorro.se.gov.br</li>
                                        <li>Telefone: (79) 99898-1288</li>
                                    </ul>
                                    <p>Informe seu ID de inscrição e CPF. O cancelamento deve ser solicitado com pelo menos 48 horas de antecedência do evento.</p>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 10 - Material -->
                        <div class="faq-item" data-category="material" id="faq-kit-evento">
                            <button class="faq-question">
                                <span>O que está incluído no kit do evento?</span>
                                <div class="faq-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    <p>O kit do participante inclui:</p>
                                    <ul>
                                        <li>Camiseta oficial do evento</li>
                                        <li>Número de identificação</li>
                                        <li>Mapa do percurso</li>
                                        <li>Informações de segurança</li>
                                        <li>Brindes dos patrocinadores</li>
                                    </ul>
                                    <p>O kit deve ser retirado na SMTT apresentando CPF e ID de inscrição.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Botão voltar -->
                    <a href="index.php" class="back-button">
                        <i class="fas fa-arrow-left"></i>
                        Voltar à Página Inicial
                    </a>
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
                                <p>(79) 99898-1288</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon address-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Address</h4>
                                <p>Na Praça Eu Amo Socorro - SE</p>
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
                                    <strong>Data:</strong> 16 de Agosto de 2026
                                </li>
                                <li style="color: #1a202c; margin-bottom: 8px; padding-left: 20px; position: relative;">
                                    <span style="position: absolute; left: 0; color: #4a90e2;">•</span>
                                    <strong>Horário:</strong> Concentração às 06:00h
                                </li>
                                <li style="color: #1a202c; margin-bottom: 8px; padding-left: 20px; position: relative;">
                                    <span style="position: absolute; left: 0; color: #4a90e2;">•</span>
                                    <strong>Local:</strong> Na Praça Eu Amo Socorro - SE
                                </li>
                                <li style="color: #1a202c; margin-bottom: 8px; padding-left: 20px; position: relative;">
                                    <span style="position: absolute; left: 0; color: #4a90e2;">•</span>
                                    <strong>Material:</strong> Retirar na SMTT com CPF e ID
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card de ajuda adicional -->
                    <div class="help-card">
                        <h3>Ainda tem dúvidas?</h3>
                        <p>Nossa equipe está pronta para ajudar você com qualquer questão sobre o evento.</p>
                        <a href="inscricao.php" class="help-btn">
                            <i class="fas fa-bicycle"></i>
                            Fazer Inscrição
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/faq.js"></script>

<?php include 'includes/footer.php'; ?>
