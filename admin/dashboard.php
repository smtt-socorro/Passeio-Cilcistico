<?php
session_start();
require_once '../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$conn = $database->getConnection();

// Buscar métricas
$metricas = [];

// Total de inscrições
$query = "SELECT COUNT(*) as total FROM inscricoes WHERE status = 'ativa'";
$stmt = $conn->query($query);
$metricas['total_inscricoes'] = $stmt->fetch()['total'];

// Inscrições hoje
$query = "SELECT COUNT(*) as total FROM inscricoes WHERE DATE(data_inscricao) = CURDATE() AND status = 'ativa'";
$stmt = $conn->query($query);
$metricas['inscricoes_hoje'] = $stmt->fetch()['total'];

// CPFs com 1 inscrição
$query = "SELECT cpf, COUNT(*) as total FROM inscricoes WHERE status = 'ativa' GROUP BY cpf HAVING COUNT(*) = 1";
$stmt = $conn->query($query);
$metricas['cpfs_multiplos'] = $stmt->rowCount();

// Últimas inscrições
$query = "SELECT * FROM inscricoes WHERE status = 'ativa' ORDER BY data_inscricao DESC LIMIT 5";
$stmt = $conn->query($query);
$ultimas_inscricoes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Painel Administrativo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-header {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            font-size: 1.5rem;
        }
        .metric-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--gray-dark);
            margin-bottom: 5px;
        }
        .metric-label {
            color: var(--gray-medium);
            font-size: 0.9rem;
        }
        .admin-nav {
            display: flex;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Admin -->
        <header class="admin-header">
            <div>
                <h1 style="color: var(--accent-blue); margin-bottom: 5px;">
                    <i class="fas fa-chart-line"></i> Dashboard Administrativo
                </h1>
                <p style="color: var(--gray-medium); margin: 0;">
                    Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </p>
            </div>
            <nav class="admin-nav">
                <a href="inscricoes.php" class="btn btn-primary">
                    <i class="fas fa-users"></i> Gerenciar Inscrições
                </a>
                <a href="export.php" class="btn btn-secondary">
                    <i class="fas fa-download"></i> Exportar
                </a>
                <a href="logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </nav>
        </header>

        <!-- Métricas -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon" style="background: var(--accent-blue); color: var(--white);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-value"><?php echo $metricas['total_inscricoes']; ?></div>
                <div class="metric-label">Total de Inscrições</div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: var(--success); color: var(--white);">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="metric-value"><?php echo $metricas['inscricoes_hoje']; ?></div>
                <div class="metric-label">Inscrições Hoje</div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: var(--warning); color: var(--white);">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="metric-value"><?php echo $metricas['cpfs_multiplos']; ?></div>
                <div class="metric-label">CPFs com 1 Inscrição  </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon" style="background: var(--primary-blue); color: var(--white);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="metric-value" style="font-size: 1.5rem;">16/08/2026</div>
                <div class="metric-label">Data do Evento</div>
            </div>
        </div>

        <!-- Últimas Inscrições -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Últimas Inscrições</h2>
                <p class="card-subtitle">5 inscrições mais recentes</p>
            </div>

            <?php if (empty($ultimas_inscricoes)): ?>
                <p style="text-align: center; color: var(--gray-medium); padding: 40px;">
                    <i class="fas fa-inbox"></i><br>
                    Nenhuma inscrição encontrada
                </p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--gray-light);">
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #dee2e6;">ID</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #dee2e6;">Nome</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #dee2e6;">Email</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #dee2e6;">Data/Hora</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #dee2e6;">Idade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimas_inscricoes as $inscricao): ?>
                                <tr>
                                    <td style="padding: 15px; border-bottom: 1px solid #dee2e6;">
                                        <strong style="color: var(--accent-blue);">
                                            <?php echo htmlspecialchars($inscricao['id_inscricao_formatado']); ?>
                                        </strong>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid #dee2e6;">
                                        <?php echo htmlspecialchars($inscricao['nome_completo']); ?>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid #dee2e6;">
                                        <?php echo htmlspecialchars($inscricao['email']); ?>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid #dee2e6;">
                                        <?php echo date('d/m/Y H:i', strtotime($inscricao['data_inscricao'])); ?>
                                    </td>
                                    <td style="padding: 15px; border-bottom: 1px solid #dee2e6;">
                                        <?php echo calcularIdade($inscricao['data_nascimento']); ?> anos
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 20px;">
                <a href="inscricoes.php" class="btn btn-primary">
                    <i class="fas fa-list"></i> Ver Todas as Inscrições
                </a>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Informações do Sistema</h2>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="info-content">
                        <h3>Banco de Dados</h3>
                        <p>MySQL - evento_bike_smtt</p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-server"></i>
                    </div>
                    <div class="info-content">
                        <h3>Sistema</h3>
                        <p>PHP <?php echo phpversion(); ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Último Acesso</h3>
                        <p><?php echo date('d/m/Y H:i'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
