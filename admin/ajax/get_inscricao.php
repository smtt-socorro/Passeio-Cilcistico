<?php
session_start();
require_once '../../config/functions.php';

// Verificar se está logado
if (!isset($_SESSION['admin_logado'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

$id = $_GET['id'] ?? '';
$action = $_GET['action'] ?? 'view';

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT * FROM inscricoes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $inscricao = $stmt->fetch();
    
    if (!$inscricao) {
        echo json_encode(['success' => false, 'message' => 'Inscrição não encontrada']);
        exit();
    }
    
    $html = '';
    $footer = '';
    
    switch($action) {
        case 'view':
            $idade = calcularIdade($inscricao['data_nascimento']);
            $html = "
                <div class='inscricao-details'>
                    <div class='detail-section'>
                        <h4><i class='fas fa-user'></i> Dados Pessoais</h4>
                        <div class='detail-grid'>
                            <div class='detail-item'>
                                <label>ID da Inscrição</label>
                                <span class='inscription-id'>{$inscricao['id_inscricao_formatado']}</span>
                            </div>
                            <div class='detail-item'>
                                <label>Nome Completo</label>
                                <span>{$inscricao['nome_completo']}</span>
                            </div>
                            <div class='detail-item'>
                                <label>CPF</label>
                                <span>" . formatarCPF($inscricao['cpf']) . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Data de Nascimento</label>
                                <span>" . date('d/m/Y', strtotime($inscricao['data_nascimento'])) . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Idade</label>
                                <span>{$idade} anos</span>
                            </div>
                            <div class='detail-item'>
                                <label>Email</label>
                                <span><a href='mailto:{$inscricao['email']}' style='color: #4a90e2; text-decoration: none;'>{$inscricao['email']}</a></span>
                            </div>
                            <div class='detail-item'>
                                <label>Telefone</label>
                                <span><a href='tel:{$inscricao['telefone']}' style='color: #4a90e2; text-decoration: none;'>{$inscricao['telefone']}</a></span>
                            </div>
                            <div class='detail-item'>
                                <label>Status</label>
                                <span class='status-badge {$inscricao['status']}'>" . ucfirst($inscricao['status']) . "</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class='detail-section'>
                        <h4><i class='fas fa-map-marker-alt'></i> Endereço Completo</h4>
                        <div class='detail-grid'>
                            <div class='detail-item'>
                                <label>CEP</label>
                                <span>" . formatarCEP($inscricao['cep']) . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Logradouro</label>
                                <span>{$inscricao['logradouro']}</span>
                            </div>
                            <div class='detail-item'>
                                <label>Número</label>
                                <span>{$inscricao['numero']}</span>
                            </div>
                            <div class='detail-item'>
                                <label>Complemento</label>
                                <span>" . ($inscricao['complemento'] ?: 'Não informado') . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Bairro</label>
                                <span>{$inscricao['bairro']}</span>
                            </div>
                            <div class='detail-item'>
                                <label>Cidade</label>
                                <span>{$inscricao['cidade']}</span>
                            </div>
                            <div class='detail-item'>
                                <label>Estado</label>
                                <span>{$inscricao['estado']}</span>
                            </div>
                            <div class='detail-item'>
                                <label>Endereço Completo</label>
                                <span>{$inscricao['logradouro']}, {$inscricao['numero']}" . ($inscricao['complemento'] ? ", {$inscricao['complemento']}" : "") . ", {$inscricao['bairro']}, {$inscricao['cidade']} - {$inscricao['estado']}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class='detail-section'>
                        <h4><i class='fas fa-info-circle'></i> Informações da Inscrição</h4>
                        <div class='detail-grid'>
                            <div class='detail-item'>
                                <label>Data da Inscrição</label>
                                <span>" . date('d/m/Y H:i:s', strtotime($inscricao['data_inscricao'])) . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Aceita Termos</label>
                                <span>" . ($inscricao['aceita_termos'] ? '✅ Sim' : '❌ Não') . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Link do Trajeto no Maps</label>
                                <span>" . ($inscricao['link_trajeto_maps'] ? "<a href='{$inscricao['link_trajeto_maps']}' target='_blank' style='color: #4a90e2; text-decoration: none;'><i class='fas fa-external-link-alt'></i> Ver no Google Maps</a>" : 'Não informado') . "</span>
                            </div>
                            <div class='detail-item'>
                                <label>Número Sequencial</label>
                                <span>#{$inscricao['numero_sequencial_id']}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class='detail-section'>
                        <h4><i class='fas fa-bicycle'></i> Informações do Evento</h4>
                        <div class='detail-grid'>
                            <div class='detail-item'>
                                <label>Data do Evento</label>
                                <span>🗓️ 16 de Agosto de 2026</span>
                            </div>
                            <div class='detail-item'>
                                <label>Local Principal</label>
                                <span>📍 Na Praça Eu Amo Socorro - SE</span>
                            </div>
                            <div class='detail-item'>
                                <label>Horário de Concentração</label>
                                <span>⏰ 06:00h</span>
                            </div>
                            <div class='detail-item'>
                                <label>Retirada do Material</label>
                                <span>🏢 SMTT de Nossa Senhora do Socorro - SE</span>
                            </div>
                        </div>
                    </div>
                </div>
            ";
            
            $footer = "
                <button type='button' class='btn-secondary' onclick='closeModal()'>
                    <i class='fas fa-times'></i> Fechar
                </button>
                <button type='button' class='btn-primary' onclick='editRegistration({$inscricao['id']})'>
                    <i class='fas fa-edit'></i> Editar Inscrição
                </button>
                <button type='button' class='btn-primary' onclick='printRegistration({$inscricao['id']})'>
                    <i class='fas fa-print'></i> Imprimir
                </button>
            ";
            break;
            
        case 'edit':
            $html = "
                <form id='editForm' onsubmit='saveRegistration(event, {$inscricao['id']})'>
                    <div class='form-section'>
                        <h4><i class='fas fa-user'></i> Dados Pessoais</h4>
                        <div class='form-row'>
                            <div class='form-group'>
                                <label>Nome Completo</label>
                                <input type='text' name='nome_completo' value='" . htmlspecialchars($inscricao['nome_completo']) . "' required>
                            </div>
                            <div class='form-group'>
                                <label>CPF</label>
                                <input type='text' name='cpf' value='" . htmlspecialchars($inscricao['cpf']) . "' readonly style='background: #f3f4f6; cursor: not-allowed;'>
                                <small style='color: #6b7280; font-size: 0.85rem;'>O CPF não pode ser alterado após a inscrição</small>
                            </div>
                            <div class='form-group'>
                                <label>Email</label>
                                <input type='email' name='email' value='" . htmlspecialchars($inscricao['email']) . "' required>
                            </div>
                            <div class='form-group'>
                                <label>Telefone</label>
                                <input type='text' name='telefone' value='" . htmlspecialchars($inscricao['telefone']) . "' required>
                            </div>
                            <div class='form-group'>
                                <label>Data de Nascimento</label>
                                <input type='date' name='data_nascimento' value='{$inscricao['data_nascimento']}' required>
                            </div>
                            <div class='form-group'>
                                <label>Status</label>
                                <select name='status' required>
                                    <option value='ativa'" . ($inscricao['status'] == 'ativa' ? ' selected' : '') . ">Ativa</option>
                                    <option value='cancelada'" . ($inscricao['status'] == 'cancelada' ? ' selected' : '') . ">Cancelada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class='form-section'>
                        <h4><i class='fas fa-map-marker-alt'></i> Endereço</h4>
                        <div class='form-row'>
                            <div class='form-group'>
                                <label>CEP</label>
                                <input type='text' name='cep' value='" . htmlspecialchars($inscricao['cep']) . "' required maxlength='9' id='edit-cep'>
                            </div>
                            <div class='form-group'>
                                <label>Logradouro</label>
                                <input type='text' name='logradouro' value='" . htmlspecialchars($inscricao['logradouro']) . "' required id='edit-logradouro'>
                            </div>
                            <div class='form-group'>
                                <label>Número</label>
                                <input type='text' name='numero' value='" . htmlspecialchars($inscricao['numero']) . "' required>
                            </div>
                            <div class='form-group'>
                                <label>Complemento</label>
                                <input type='text' name='complemento' value='" . htmlspecialchars($inscricao['complemento']) . "'>
                            </div>
                            <div class='form-group'>
                                <label>Bairro</label>
                                <input type='text' name='bairro' value='" . htmlspecialchars($inscricao['bairro']) . "' required id='edit-bairro'>
                            </div>
                            <div class='form-group'>
                                <label>Cidade</label>
                                <input type='text' name='cidade' value='" . htmlspecialchars($inscricao['cidade']) . "' required id='edit-cidade'>
                            </div>
                            <div class='form-group'>
                                <label>Estado</label>
                                <select name='estado' required id='edit-estado'>
                                    <option value='AC'" . ($inscricao['estado'] == 'AC' ? ' selected' : '') . ">Acre</option>
                                    <option value='AL'" . ($inscricao['estado'] == 'AL' ? ' selected' : '') . ">Alagoas</option>
                                    <option value='AP'" . ($inscricao['estado'] == 'AP' ? ' selected' : '') . ">Amapá</option>
                                    <option value='AM'" . ($inscricao['estado'] == 'AM' ? ' selected' : '') . ">Amazonas</option>
                                    <option value='BA'" . ($inscricao['estado'] == 'BA' ? ' selected' : '') . ">Bahia</option>
                                    <option value='CE'" . ($inscricao['estado'] == 'CE' ? ' selected' : '') . ">Ceará</option>
                                    <option value='DF'" . ($inscricao['estado'] == 'DF' ? ' selected' : '') . ">Distrito Federal</option>
                                    <option value='ES'" . ($inscricao['estado'] == 'ES' ? ' selected' : '') . ">Espírito Santo</option>
                                    <option value='GO'" . ($inscricao['estado'] == 'GO' ? ' selected' : '') . ">Goiás</option>
                                    <option value='MA'" . ($inscricao['estado'] == 'MA' ? ' selected' : '') . ">Maranhão</option>
                                    <option value='MT'" . ($inscricao['estado'] == 'MT' ? ' selected' : '') . ">Mato Grosso</option>
                                    <option value='MS'" . ($inscricao['estado'] == 'MS' ? ' selected' : '') . ">Mato Grosso do Sul</option>
                                    <option value='MG'" . ($inscricao['estado'] == 'MG' ? ' selected' : '') . ">Minas Gerais</option>
                                    <option value='PA'" . ($inscricao['estado'] == 'PA' ? ' selected' : '') . ">Pará</option>
                                    <option value='PB'" . ($inscricao['estado'] == 'PB' ? ' selected' : '') . ">Paraíba</option>
                                    <option value='PR'" . ($inscricao['estado'] == 'PR' ? ' selected' : '') . ">Paraná</option>
                                    <option value='PE'" . ($inscricao['estado'] == 'PE' ? ' selected' : '') . ">Pernambuco</option>
                                    <option value='PI'" . ($inscricao['estado'] == 'PI' ? ' selected' : '') . ">Piauí</option>
                                    <option value='RJ'" . ($inscricao['estado'] == 'RJ' ? ' selected' : '') . ">Rio de Janeiro</option>
                                    <option value='RN'" . ($inscricao['estado'] == 'RN' ? ' selected' : '') . ">Rio Grande do Norte</option>
                                    <option value='RS'" . ($inscricao['estado'] == 'RS' ? ' selected' : '') . ">Rio Grande do Sul</option>
                                    <option value='RO'" . ($inscricao['estado'] == 'RO' ? ' selected' : '') . ">Rondônia</option>
                                    <option value='RR'" . ($inscricao['estado'] == 'RR' ? ' selected' : '') . ">Roraima</option>
                                    <option value='SC'" . ($inscricao['estado'] == 'SC' ? ' selected' : '') . ">Santa Catarina</option>
                                    <option value='SP'" . ($inscricao['estado'] == 'SP' ? ' selected' : '') . ">São Paulo</option>
                                    <option value='SE'" . ($inscricao['estado'] == 'SE' ? ' selected' : '') . ">Sergipe</option>
                                    <option value='TO'" . ($inscricao['estado'] == 'TO' ? ' selected' : '') . ">Tocantins</option>
                                </select>
                            </div>
                            <div class='form-group' style='grid-column: 1 / -1;'>
                                <label>Link do Trajeto no Google Maps</label>
                                <input type='url' name='link_trajeto_maps' value='" . htmlspecialchars($inscricao['link_trajeto_maps']) . "' placeholder='https://maps.app.goo.gl/...'>
                                <small style='color: #6b7280; font-size: 0.85rem;'>Link personalizado fornecido pelo participante (opcional)</small>
                            </div>
                        </div>
                    </div>
                </form>
                
                <script>
                // Máscara para CEP
                document.getElementById('edit-cep').addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                });
                
                // Integração ViaCEP para edição
                document.getElementById('edit-cep').addEventListener('blur', function() {
                    const cep = this.value.replace(/\D/g, '');
                    if (cep.length === 8) {
                        fetch(`https://viacep.com.br/ws/\${cep}/json/`)
                            .then(response => response.json())
                            .then(data => {
                                if (!data.erro) {
                                    document.getElementById('edit-logradouro').value = data.logradouro;
                                    document.getElementById('edit-bairro').value = data.bairro;
                                    document.getElementById('edit-cidade').value = data.localidade;
                                    document.getElementById('edit-estado').value = data.uf;
                                }
                            })
                            .catch(error => console.error('Erro ao buscar CEP:', error));
                    }
                });
                </script>
            ";
            
            $footer = "
                <button type='button' class='btn-secondary' onclick='closeModal()'>
                    <i class='fas fa-times'></i> Cancelar
                </button>
                <button type='submit' form='editForm' class='btn-primary'>
                    <i class='fas fa-save'></i> Salvar Alterações
                </button>
            ";
            break;
            
        case 'delete':
            $html = "
                <div class='delete-confirmation'>
                    <div class='warning-icon'>
                        <i class='fas fa-exclamation-triangle'></i>
                    </div>
                    <h3>Confirmar Exclusão</h3>
                    <p>Tem certeza que deseja excluir a inscrição de:</p>
                    <div style='background: #f8fafc; padding: 20px; border-radius: 12px; margin: 20px 0; border: 2px solid #e2e8f0;'>
                        <p><strong>Nome:</strong> {$inscricao['nome_completo']}</p>
                        <p><strong>ID:</strong> {$inscricao['id_inscricao_formatado']}</p>
                        <p><strong>CPF:</strong> " . formatarCPF($inscricao['cpf']) . "</p>
                        <p><strong>Email:</strong> {$inscricao['email']}</p>
                    </div>
                    <p><small style='color: #ef4444; font-weight: 600;'>Esta ação não pode ser desfeita. A inscrição será marcada como cancelada.</small></p>
                </div>
            ";
            
            $footer = "
                <button type='button' class='btn-secondary' onclick='closeModal()'>
                    <i class='fas fa-times'></i> Cancelar
                </button>
                <button type='button' class='btn-danger' onclick='confirmDelete({$inscricao['id']})'>
                    <i class='fas fa-trash'></i> Confirmar Exclusão
                </button>
            ";
            break;
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'footer' => $footer,
        'inscricao' => $inscricao
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Função auxiliar para calcular idade
function calcularIdade($dataNascimento) {
    $nascimento = new DateTime($dataNascimento);
    $hoje = new DateTime();
    $idade = $hoje->diff($nascimento);
    return $idade->y;
}

// Função auxiliar para formatar CPF
function formatarCPF($cpf) {
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

// Função auxiliar para formatar CEP
function formatarCEP($cep) {
    return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
}
?>
