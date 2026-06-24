// JavaScript específico para Admin Dashboard
// Evento Bike SMTT Socorro

document.addEventListener('DOMContentLoaded', function() {
    initAdminDashboard();
});

function initAdminDashboard() {
    initMetricsCards();
    initRealtimeUpdates();
    initTableActions();
    initCharts();
    initNotifications();
}

// Inicializar cards de métricas
function initMetricsCards() {
    const metricCards = document.querySelectorAll('.metric-card');
    
    metricCards.forEach((card, index) => {
        // Animação de entrada escalonada
        card.style.animationDelay = `${index * 0.1}s`;
        
        // Efeito hover com dados dinâmicos
        card.addEventListener('mouseenter', function() {
            const value = this.querySelector('.metric-value');
            if (value) {
                value.style.transform = 'scale(1.05)';
                value.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const value = this.querySelector('.metric-value');
            if (value) {
                value.style.transform = 'scale(1)';
            }
        });
    });
    
    // Atualizar métricas
    updateMetrics();
}

// Atualizar métricas via AJAX
function updateMetrics() {
    fetch('ajax/get_metrics.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualizar total de inscrições
                updateMetricValue('total-inscricoes', data.metrics.total_inscricoes, data.metrics.total_change);
                
                // Atualizar inscrições hoje
                updateMetricValue('inscricoes-hoje', data.metrics.inscricoes_hoje, data.metrics.hoje_change);
                
                // Atualizar bairros cadastrados
                updateMetricValue('bairros-populares', data.metrics.bairros_populares, data.metrics.bairros_change);
                
                // Atualizar média de idade
                updateMetricValue('media-idade', data.metrics.media_idade + ' anos', data.metrics.idade_change);
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar métricas:', error);
        });
}

// Atualizar valor de métrica com animação
function updateMetricValue(metricId, newValue, change) {
    const valueElement = document.querySelector(`[data-metric="${metricId}"] .metric-value`);
    const changeElement = document.querySelector(`[data-metric="${metricId}"] .metric-change`);
    
    if (valueElement) {
        // Animação de contagem
        animateValue(valueElement, valueElement.textContent, newValue, 1000);
    }
    
    if (changeElement && change !== undefined) {
        const isPositive = change >= 0;
        changeElement.className = `metric-change ${isPositive ? 'positive' : 'negative'}`;
        changeElement.innerHTML = `
            <i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i>
            ${Math.abs(change)}%
        `;
    }
}

// Animação de contagem de números
function animateValue(element, start, end, duration) {
    const startNum = parseInt(start.toString().replace(/\D/g, '')) || 0;
    const endNum = parseInt(end.toString().replace(/\D/g, '')) || 0;
    const range = endNum - startNum;
    const startTime = performance.now();
    
    function updateValue(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const current = Math.floor(startNum + (range * progress));
        
        if (end.toString().includes('anos')) {
            element.textContent = current + ' anos';
        } else {
            element.textContent = current.toLocaleString('pt-BR');
        }
        
        if (progress < 1) {
            requestAnimationFrame(updateValue);
        }
    }
    
    requestAnimationFrame(updateValue);
}

// Atualizações em tempo real
function initRealtimeUpdates() {
    // Atualizar métricas a cada 30 segundos
    setInterval(updateMetrics, 30000);
    
    // Atualizar tabela de inscrições recentes a cada 60 segundos
    setInterval(updateRecentRegistrations, 60000);
    
    // Mostrar timestamp da última atualização
    updateLastRefresh();
    setInterval(updateLastRefresh, 1000);
}

// Atualizar timestamp
function updateLastRefresh() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('pt-BR');
    
    let refreshElement = document.getElementById('last-refresh');
    if (!refreshElement) {
        refreshElement = document.createElement('small');
        refreshElement.id = 'last-refresh';
        refreshElement.style.color = '#9ca3af';
        refreshElement.style.fontSize = '0.8rem';
        
        const subtitle = document.querySelector('.dashboard-subtitle');
        if (subtitle) {
            subtitle.appendChild(document.createElement('br'));
            subtitle.appendChild(refreshElement);
        }
    }
    
    refreshElement.textContent = `Última atualização: ${timeString}`;
}

// Atualizar tabela de inscrições recentes
function updateRecentRegistrations() {
    const tableBody = document.querySelector('#recent-table tbody');
    if (!tableBody) return;
    
    tableBody.classList.add('loading');
    
    fetch('ajax/get_recent_registrations.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                tableBody.innerHTML = '';
                
                if (data.registrations.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Nenhuma inscrição encontrada</h3>
                                <p>As inscrições aparecerão aqui conforme forem realizadas</p>
                            </td>
                        </tr>
                    `;
                } else {
                    data.registrations.forEach(registration => {
                        const row = createRegistrationRow(registration);
                        tableBody.appendChild(row);
                    });
                }
            }
        })
        .catch(error => {
            console.error('Erro ao atualizar inscrições:', error);
            showToast('Erro ao atualizar dados', 'error');
        })
        .finally(() => {
            tableBody.classList.remove('loading');
        });
}

// Criar linha da tabela
function createRegistrationRow(registration) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <span class="inscription-id">${registration.id_inscricao_formatado}</span>
        </td>
        <td>${registration.nome_completo}</td>
        <td>${formatCPF(registration.cpf)}</td>
        <td>${registration.email}</td>
        <td>${registration.bairro}</td>
        <td>
            <div class="action-buttons">
                <button class="btn-action btn-view" onclick="viewRegistration('${registration.id}')" title="Visualizar">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn-action btn-edit" onclick="editRegistration('${registration.id}')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-action btn-delete" onclick="deleteRegistration('${registration.id}')" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    `;
    
    // Animação de entrada
    row.style.opacity = '0';
    row.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        row.style.transition = 'all 0.3s ease';
        row.style.opacity = '1';
        row.style.transform = 'translateY(0)';
    }, 100);
    
    return row;
}

// Ações da tabela
function initTableActions() {
    // Implementar ações já definidas globalmente
}

// Funções para modal de inscrições
function viewRegistration(id) {
    openModal('view', id);
}

function editRegistration(id) {
    openModal('edit', id);
}

function deleteRegistration(id) {
    if (confirm('Tem certeza que deseja excluir esta inscrição?')) {
        openModal('delete', id);
    }
}

function openModal(action, id) {
    const modal = document.getElementById('inscricaoModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const modalFooter = document.getElementById('modalFooter');
    
    // Mostrar loading
    modalBody.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>';
    modalFooter.innerHTML = '';
    
    // Definir título
    switch(action) {
        case 'view':
            modalTitle.textContent = 'Detalhes da Inscrição';
            break;
        case 'edit':
            modalTitle.textContent = 'Editar Inscrição';
            break;
        case 'delete':
            modalTitle.textContent = 'Excluir Inscrição';
            break;
    }
    
    modal.style.display = 'flex';
    
    // Carregar dados via AJAX
    fetch(`ajax/get_inscricao.php?id=${id}&action=${action}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalBody.innerHTML = data.html;
                modalFooter.innerHTML = data.footer;
            } else {
                modalBody.innerHTML = '<p class="error">Erro ao carregar dados: ' + data.message + '</p>';
            }
        })
        .catch(error => {
            modalBody.innerHTML = '<p class="error">Erro de conexão</p>';
            console.error('Erro:', error);
        });
}

function closeModal() {
    document.getElementById('inscricaoModal').style.display = 'none';
}

// Salvar alterações da inscrição
function saveRegistration(event, id) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    formData.append('id', id);
    formData.append('action', 'update');
    
    // Mostrar loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    submitBtn.disabled = true;
    
    fetch('ajax/save_inscricao.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Inscrição atualizada com sucesso!', 'success');
            closeModal();
            updateRecentRegistrations();
        } else {
            showToast('Erro ao atualizar: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Erro de conexão', 'error');
        console.error('Erro:', error);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Confirmar exclusão
function confirmDelete(id) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('action', 'delete');
    
    fetch('ajax/save_inscricao.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Inscrição excluída com sucesso!', 'success');
            closeModal();
            updateRecentRegistrations();
        } else {
            showToast('Erro ao excluir: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Erro de conexão', 'error');
        console.error('Erro:', error);
    });
}

// Fechar modal ao clicar fora
document.addEventListener('click', function(e) {
    const modal = document.getElementById('inscricaoModal');
    if (e.target === modal) {
        closeModal();
    }
});

// Inicializar gráficos (se necessário)
function initCharts() {
    // Placeholder para gráficos futuros
    console.log('Charts initialized');
}

// Sistema de notificações
function initNotifications() {
    // Verificar notificações pendentes
    checkPendingNotifications();
    
    // Verificar a cada 5 minutos
    setInterval(checkPendingNotifications, 300000);
}

function checkPendingNotifications() {
    fetch('ajax/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    showToast(notification.message, notification.type);
                });
            }
        })
        .catch(error => {
            console.error('Erro ao verificar notificações:', error);
        });
}

// Função para formatar CPF
function formatCPF(cpf) {
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

// Toast notifications
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${getToastIcon(type)}"></i>
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    Object.assign(toast.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        background: getToastColor(type),
        color: 'white',
        padding: '16px 20px',
        borderRadius: '12px',
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
        zIndex: '10000',
        boxShadow: '0 8px 25px rgba(0,0,0,0.15)',
        animation: 'slideInRight 0.3s ease',
        fontWeight: '500',
        maxWidth: '400px'
    });

    document.body.appendChild(toast);

    // Auto remove após 5 segundos
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

function getToastIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-triangle',
        warning: 'exclamation-circle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function getToastColor(type) {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    return colors[type] || '#3b82f6';
}

// Adicionar estilos de animação
const toastStyles = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .toast-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background 0.2s ease;
    }
    
    .toast-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .loading-spinner {
        text-align: center;
        padding: 40px;
        color: #6b7280;
        font-size: 1.1rem;
    }
    
    .loading-spinner i {
        font-size: 1.5rem;
        margin-right: 10px;
    }
`;

// Injetar estilos
if (!document.getElementById('toast-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'toast-styles';
    styleSheet.textContent = toastStyles;
    document.head.appendChild(styleSheet);
}

// Funções de exportação
function exportData(format) {
    const url = `export.php?format=${format}`;
    window.open(url, '_blank');
    showToast(`Exportando dados em formato ${format.toUpperCase()}...`, 'info');
}

// Atualização manual
function refreshDashboard() {
    showToast('Atualizando dashboard...', 'info');
    updateMetrics();
    updateRecentRegistrations();
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + R para refresh
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        refreshDashboard();
    }
    
    // Ctrl/Cmd + E para exportar
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportData('csv');
    }
    
    // ESC para fechar modal
    if (e.key === 'Escape') {
        closeModal();
    }
});
