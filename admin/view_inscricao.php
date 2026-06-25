<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    echo '<p style="color: var(--danger);">Acesso negado</p>';
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    echo '<p style="color: var(--danger);">ID inválido</p>';
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT * FROM inscricoes WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();

$inscricao = $stmt->fetch();

if (!$inscricao) {
    echo '<p style="color: var(--danger);">Inscrição não encontrada</p>';
    exit();
}
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
    <!-- Dados Pessoais -->
    <div>
        <h4 style="color: var(--accent-blue); margin-bottom: 15px; border-bottom: 2px solid var(--light-blue); padding-bottom: 8px;">
            <i class="fas fa-user"></i> Dados Pessoais
        </h4>
        <div style="space-y: 12px;">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($inscricao['id_inscricao_formatado']); ?></p>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($inscricao['nome_completo']); ?></p>
            <p><strong>CPF:</strong> <?php echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $inscricao['cpf']); ?></p>
            <p><strong>Data de Nascimento:</strong> <?php echo date('d/m/Y', strtotime($inscricao['data_nascimento'])); ?></p>
            <p><strong>Idade:</strong> <?php echo calcularIdade($inscricao['data_nascimento']); ?> anos</p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($inscricao['email']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($inscricao['telefone']); ?></p>
        </div>
    </div>

    <!-- Endereço -->
    <div>
        <h4 style="color: var(--accent-blue); margin-bottom: 15px; border-bottom: 2px solid var(--light-blue); padding-bottom: 8px;">
            <i class="fas fa-map-marker-alt"></i> Endereço
        </h4>
        <div style="space-y: 12px;">
            <p><strong>CEP:</strong> <?php echo preg_replace('/(\d{5})(\d{3})/', '$1-$2', $inscricao['cep']); ?></p>
            <p><strong>Logradouro:</strong> <?php echo htmlspecialchars($inscricao['logradouro']); ?></p>
            <p><strong>Número:</strong> <?php echo htmlspecialchars($inscricao['numero']); ?></p>
            <?php if ($inscricao['complemento']): ?>
                <p><strong>Complemento:</strong> <?php echo htmlspecialchars($inscricao['complemento']); ?></p>
            <?php endif; ?>
            <p><strong>Bairro:</strong> <?php echo htmlspecialchars($inscricao['bairro']); ?></p>
            <p><strong>Cidade:</strong> <?php echo htmlspecialchars($inscricao['cidade']); ?></p>
            <p><strong>Estado:</strong> <?php echo htmlspecialchars($inscricao['estado']); ?></p>
        </div>
    </div>
</div>

<!-- Informações Adicionais -->
<div style="margin-top: 30px;">
    <h4 style="color: var(--accent-blue); margin-bottom: 15px; border-bottom: 2px solid var(--light-blue); padding-bottom: 8px;">
        <i class="fas fa-info-circle"></i> Informações Adicionais
    </h4>
    
    <div style="display: grid; gap: 15px;">
        <?php if ($inscricao['link_trajeto_maps']): ?>
            <div style="background: var(--light-blue); padding: 15px; border-radius: 8px;">
                <p><strong><i class="fas fa-route"></i> Trajeto Personalizado:</strong></p>
                <p>
                    <a href="<?php echo htmlspecialchars($inscricao['link_trajeto_maps']); ?>" 
                       target="_blank" style="color: var(--accent-blue);">
                        Ver no Google Maps <i class="fas fa-external-link-alt"></i>
                    </a>
                </p>
            </div>
        <?php endif; ?>

        <div style="background: var(--light-blue); padding: 15px; border-radius: 8px;">
            <p><strong><i class="fas fa-calendar-plus"></i> Data da Inscrição:</strong></p>
            <p><?php echo date('d/m/Y \à\s H:i', strtotime($inscricao['data_inscricao'])); ?></p>
        </div>

        <div style="background: var(--light-blue); padding: 15px; border-radius: 8px;">
            <p><strong><i class="fas fa-flag"></i> Status:</strong></p>
            <p>
                <span class="status-badge status-<?php echo $inscricao['status']; ?>">
                    <?php echo ucfirst($inscricao['status']); ?>
                </span>
            </p>
        </div>

        <div style="background: var(--light-blue); padding: 15px; border-radius: 8px;">
            <p><strong><i class="fas fa-check-circle"></i> Termos Aceitos:</strong></p>
            <p><?php echo $inscricao['aceita_termos'] ? 'Sim' : 'Não'; ?></p>
        </div>
    </div>
</div>

<style>
.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}
.status-ativa {
    background: #d4edda;
    color: #155724;
}
.status-cancelada {
    background: #f8d7da;
    color: #721c24;
}
</style>
