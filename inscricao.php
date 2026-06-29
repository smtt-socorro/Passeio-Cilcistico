<?php
require_once 'config/functions.php';

$page_title = "Inscrição no Evento";
$admin_area = false;

// Verificar se há mensagem de sucesso via GET
$message = '';
$messageType = '';

if (isset($_GET['success']) && $_GET['success'] === '1' && isset($_GET['id'])) {
    $message = htmlspecialchars($_GET['id']);
    $messageType = 'success';
} elseif (isset($_GET['error']) && !empty($_GET['error'])) {
    $message = htmlspecialchars($_GET['error']);
    $messageType = 'error';
}

if ($_POST) {
    try {
        // Validações
        $nome = sanitize($_POST['nome_completo']);
        $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
        $data_nascimento = $_POST['data_nascimento'];
        $email = sanitize($_POST['email']);
        $telefone = sanitize($_POST['telefone']);
        $cep = preg_replace('/[^0-9]/', '', $_POST['cep']);
        $logradouro = sanitize($_POST['logradouro']);
        $numero = sanitize($_POST['numero']);
        $complemento = sanitize($_POST['complemento']);
        $bairro = sanitize($_POST['bairro']);
        $cidade = sanitize($_POST['cidade']);
        $estado = sanitize($_POST['estado']);
        $aceita_termos = isset($_POST['aceita_termos']) ? 1 : 0;

        // Validar e converter data de nascimento dd/mm/yyyy para yyyy-mm-dd
        if (!empty($data_nascimento)) {
            if (!validarDataNascimento($data_nascimento)) {
                throw new Exception('Data de nascimento inválida. Use o formato dd/mm/aaaa');
            }
            $data_nascimento = converterDataParaMySQL($data_nascimento);
        }

        // Validações básicas
        if (empty($nome)) {
            throw new Exception('Nome completo é obrigatório');
        }

        if (!validarCPF($cpf)) {
            throw new Exception('CPF inválido');
        }

        // Verificar limite de inscrições por CPF
        if (!verificarLimiteInscricoes($cpf)) {
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "SELECT COUNT(*) as total, STRING_AGG(id_inscricao_formatado, ',') as ids FROM inscricoes WHERE cpf = :cpf AND status = 'ativa'";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['total'] >= 2) {
                throw new Exception('Este CPF já possui o máximo de 1 inscrições ativas permitidas');
            } else {
                throw new Exception('Este CPF já possui uma inscrição ativa no evento');
            }
        }

        if (empty($data_nascimento)) {
            throw new Exception('Data de nascimento é obrigatória');
        }

        if (!validarEmail($email)) {
            throw new Exception('Email inválido');
        }

        if (empty($telefone)) {
            throw new Exception('Telefone é obrigatório');
        }

        if (strlen($cep) !== 8) {
            throw new Exception('CEP inválido');
        }

        if (empty($logradouro)) {
            throw new Exception('Logradouro é obrigatório');
        }

        if (empty($numero)) {
            throw new Exception('Número é obrigatório');
        }

        if (empty($bairro)) {
            throw new Exception('Bairro é obrigatório');
        }

        if (empty($cidade)) {
            throw new Exception('Cidade é obrigatória');
        }

        if (empty($estado)) {
            throw new Exception('Estado é obrigatório');
        }

        if (!$aceita_termos) {
            throw new Exception('Você deve aceitar os termos de responsabilidade');
        }

        // Gerar ID sequencial
        $idData = gerarProximoID();

        // Inserir no banco
        $database = new Database();
        $conn = $database->getConnection();

        $query = "INSERT INTO inscricoes (
            id_inscricao_formatado, numero_sequencial_id, nome_completo, cpf, 
            data_nascimento, email, telefone, cep, logradouro, numero, 
            complemento, bairro, cidade, estado, aceita_termos, data_inscricao, status
        ) VALUES (
            :id_formatado, :numero_sequencial, :nome, :cpf, :data_nascimento, 
            :email, :telefone, :cep, :logradouro, :numero, :complemento, 
            :bairro, :cidade, :estado, :aceita_termos, NOW(), 'ativa'
        )";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_formatado', $idData['id_formatado']);
        $stmt->bindParam(':numero_sequencial', $idData['numero']);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':data_nascimento', $data_nascimento);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':cep', $cep);
        $stmt->bindParam(':logradouro', $logradouro);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':complemento', $complemento);
        $stmt->bindParam(':bairro', $bairro);
        $stmt->bindParam(':cidade', $cidade);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':aceita_termos', $aceita_termos);

        if ($stmt->execute()) {
            // Log da atividade
            logAtividade('Inscrição realizada', "ID: {$idData['id_formatado']}, CPF: {$cpf}");
            
            // REDIRECT PARA EVITAR RESUBMISSÃO
            header('Location: inscricao.php?success=1&id=' . urlencode($idData['id_formatado']), true, 303);
            exit();
        }

    } catch (Exception $e) {
        // REDIRECT COM ERRO PARA EVITAR RESUBMISSÃO
        header('Location: inscricao.php?error=' . urlencode($e->getMessage()), true, 303);
        exit();
    }
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/inscricao.css">

<!-- Página de Inscrição -->
<section class="inscription-page">
    <div class="inscription-container">
        <!-- Header Get in Touch - Baseado nas imagens -->
        <div class="get-in-touch-header">
            <!--<p class="get-in-touch-subtitle">Get in Touch</p>-->
            <h1 class="get-in-touch-title">Inscreva-se</h1>
            <!--<p class="get-in-touch-description">
                Tem dúvidas sobre o evento ou deseja se inscrever? Nossa equipe está pronta para ajudar. 
                Preencha o formulário e participe do maior evento de ciclismo da região.
            </p>-->
        </div>

        <!-- Layout em duas colunas -->
        <div class="inscription-layout">
            
            <!-- Coluna do Formulário -->
            <div class="form-column">
                <form id="inscricaoForm" method="POST">
                    
                    <!-- Dados Pessoais -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i> Dados Pessoais
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nome_completo">Nome Completo</label>
                                <input type="text" id="nome_completo" name="nome_completo" 
                                       placeholder="Digite seu nome completo" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cpf">CPF</label>
                                <input type="text" id="cpf" name="cpf" 
                                       placeholder="000.000.000-00" required>
                                <div class="field-error" id="cpf-error"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="data_nascimento">Data de Nascimento</label>
                                <input type="text" id="data_nascimento" name="data_nascimento" 
                                       placeholder="dd/mm/aaaa" required maxlength="10">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" 
                                       placeholder="seu.email@exemplo.com" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone/Celular</label>
                            <input type="tel" id="telefone" name="telefone" 
                                   placeholder="(00) 00000-0000" required>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-map-marker-alt"></i> Endereço
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group" style="position: relative;">
                                <label for="cep">CEP</label>
                                <input type="text" id="cep" name="cep" 
                                       placeholder="00000-000" required>
                                <div class="loading-cep" id="cep-loading">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="logradouro">Logradouro</label>
                                <input type="text" id="logradouro" name="logradouro" 
                                       placeholder="Rua, Avenida..." required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero" 
                                       placeholder="123" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="complemento">Complemento</label>
                                <input type="text" id="complemento" name="complemento" 
                                       placeholder="Apt, Casa...">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="bairro">Bairro</label>
                                <input type="text" id="bairro" name="bairro" 
                                       placeholder="Nome do bairro" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" 
                                       placeholder="Nome da cidade" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" required>
                                <option value="">Selecione o Estado</option>
                                <option value="SE" selected>Sergipe</option>
                                <option value="AL">Alagoas</option>
                                <option value="BA">Bahia</option>
                                <option value="PE">Pernambuco</option>
                            </select>
                        </div>
                    </div>

                    <!-- SEÇÃO DE TERMOS -->
                    <div class="terms-section">
                        <div class="terms-wrapper">
                            <input type="checkbox" name="aceita_termos" id="aceita_termos" class="terms-checkbox" required>
                            <label for="aceita_termos" class="terms-text">
                                Eu li e aceito os 
                                <a href="termos.php" target="_blank">Termos de Responsabilidade</a> 
                                do evento <span class="terms-required">*</span>
                            </label>
                        </div>
                    </div>

                    <!-- Botão de Envio -->
                    <div class="form-submit">
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-bicycle"></i>
                            Realizar Inscrição
                        </button>
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
                            <h4>Contato</h4>
                            <p>(79) 99898-1288</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="contact-icon address-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Endereço</h4>
                            <p>Av. Nossa Sra. do Socorro, 30 - João Alves, Nossa Sra. do Socorro - SE, 49155-434</p>
                        </div>
                    </div>

                    <!-- Informações do Evento -->
                    <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid rgba(0, 0, 0, 0.08);">
                        <h4 style="color: var(--accent-blue); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-calendar-alt"></i> Informações do Evento
                        </h4>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="color: var(--gray-dark); margin-bottom: 8px; padding-left: 20px; position: relative;">
                                <span style="position: absolute; left: 0; color: var(--accent-blue);">•</span>
                                <strong>Data:</strong> 16 de Agosto de 2026
                            </li>
                            <li style="color: var(--gray-dark); margin-bottom: 8px; padding-left: 20px; position: relative;">
                                <span style="position: absolute; left: 0; color: var(--accent-blue);">•</span>
                                <strong>Horário:</strong> Concentração às 06:00h
                            </li>
                            <li style="color: var(--gray-dark); margin-bottom: 8px; padding-left: 20px; position: relative;">
                                <span style="position: absolute; left: 0; color: var(--accent-blue);">•</span>
                                <strong>Local:</strong> Na Praça Eu Amo Socorro - SE
                            </li>
                            <li style="color: var(--gray-dark); margin-bottom: 8px; padding-left: 20px; position: relative;">
                                <span style="position: absolute; left: 0; color: var(--accent-blue);">•</span>
                                <strong>Importante:</strong> Uso de capacete obrigatório
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Sucesso Refinado -->
<div id="successModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <button class="modal-close" type="button" aria-label="Fechar modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <h2 class="modal-title">Inscrição Realizada com Sucesso!</h2>
                <p class="modal-subtitle">Parabéns! Sua participação no evento foi confirmada 🎉</p>
                
                <div class="inscription-id-display">
                    <span class="id-label">Seu ID de Inscrição</span>
                    <div class="id-value" id="inscricao-id-modal">
                        <!-- ID será inserido aqui -->
                    </div>
                </div>

                <div class="important-info">
                    <h4 style="color: var(--accent-blue); margin-bottom: 10px;">
                        ℹ️ Retirada do Material
                    </h4>
                    <p style="margin: 0; color: var(--gray-dark); line-height: 1.5;">
                        O material do evento deverá ser retirado na <strong>SMTT de Nossa Senhora do Socorro - SE</strong>, 
                        apresentando seu CPF e ID de inscrição.
                    </p>
                </div>
            </div>

            <div class="modal-footer">
                <a href="verificar_inscricao.php" class="btn-primary-modal">
                    <i class="fas fa-search"></i>
                    Consultar Minha Inscrição
                </a>
                <button class="btn-secondary-modal" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Erro para CPF Duplicado -->
<div id="errorModal" class="error-modal">
    <div class="error-modal-content">
        <div class="error-modal-header">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 id="error-modal-title">CPF Já Cadastrado</h3>
            <button class="modal-close" type="button" aria-label="Fechar modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="error-modal-body">
            <p id="error-modal-message">Este CPF já possui inscrições ativas no evento.</p>
            
            <div id="existing-registrations" style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin: 20px 0;">
                <h4 style="color: var(--accent-blue); margin-bottom: 10px;">Inscrições Existentes:</h4>
                <div id="registration-ids" style="font-weight: 600; color: var(--gray-dark);"></div>
            </div>
            
            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <p style="margin: 0; color: var(--gray-dark); font-size: 0.95rem;">
                    <i class="fas fa-info-circle" style="color: var(--accent-blue);"></i>
                    Você pode consultar suas inscrições existentes ou entrar em contato conosco para esclarecimentos.
                </p>
            </div>
        </div>
        
        <div class="error-modal-footer">
            <button class="btn-primary-modal" onclick="window.location.href='verificar_inscricao.php'">
                <i class="fas fa-search"></i>
                Consultar Minhas Inscrições
            </button>
            <button class="btn-secondary-modal" onclick="closeErrorModal()">
                <i class="fas fa-times"></i>
                Fechar
            </button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <h3>Processando sua inscrição...</h3>
        <p>Aguarde enquanto confirmamos seus dados</p>
    </div>
</div>

<script src="assets/js/validation.js"></script>
<script src="assets/js/viacep.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/inscricao.js"></script>

<?php
// JavaScript para resultado do formulário
if ($messageType === 'success') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showSuccessModal('$message');
        });
    </script>";
} elseif ($messageType === 'error') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('loadingOverlay').style.display = 'none';
            showToast('$message', 'error');
            
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class=\"fas fa-bicycle\"></i> Realizar Inscrição';
                submitBtn.disabled = false;
            }
        });
    </script>";
}

include 'includes/footer.php';
?>
