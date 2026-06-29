// admin/assets/js/reports.js - Sistema de Relatórios Avançados
// Evento Bike Socorro

document.addEventListener('DOMContentLoaded', function() {
    const filtersForm = document.getElementById('filtersForm');
    const resetFilters = document.getElementById('resetFilters');
    const statsArea = document.getElementById('statsArea');
    const resultsArea = document.getElementById('resultsArea');
    const loadingReport = document.getElementById('loadingReport');
    const reportTable = document.getElementById('reportTable');
    const reportTableBody = document.getElementById('reportTableBody');
    const emptyReport = document.getElementById('emptyReport');
    const statsGrid = document.getElementById('statsGrid');
    const printReport = document.getElementById('printReport');
    const exportCSV = document.getElementById('exportCSV');
    const exportExcel = document.getElementById('exportExcel');
    const printModal = document.getElementById('printModal');
    
    let currentReportData = null;
    let currentStats = null;

    // Verificar se todos os elementos necessários existem
    if (!filtersForm || !statsArea || !resultsArea) {
        console.error('Elementos necessários não encontrados na página');
        return;
    }

    // Submissão do formulário de filtros
    filtersForm.addEventListener('submit', function(e) {
        e.preventDefault();
        generateReport();
    });

    // Reset dos filtros
    resetFilters.addEventListener('click', function() {
        filtersForm.reset();
        statsArea.style.display = 'none';
        resultsArea.style.display = 'none';
        currentReportData = null;
        currentStats = null;
    });

    // Botões de ação
    printReport.addEventListener('click', function() {
        if (currentReportData && currentReportData.length > 0) {
            printModal.style.display = 'flex';
        } else {
            showToast('Nenhum dado disponível para impressão', 'warning');
        }
    });

    exportCSV.addEventListener('click', function() {
        if (currentReportData && currentReportData.length > 0) {
            exportToCSV();
        } else {
            showToast('Nenhum dado disponível para exportação', 'warning');
        }
    });

    exportExcel.addEventListener('click', function() {
        if (currentReportData && currentReportData.length > 0) {
            exportToExcel();
        } else {
            showToast('Nenhum dado disponível para exportação', 'warning');
        }
    });

    function generateReport() {
        // Mostrar loading
        statsArea.style.display = 'block';
        resultsArea.style.display = 'block';
        loadingReport.style.display = 'flex';
        reportTable.style.display = 'none';
        emptyReport.style.display = 'none';

        // Coletar dados do formulário
        const formData = new FormData(filtersForm);
        
        // Converter para URLSearchParams para envio
        const params = new URLSearchParams();
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                params.append(key, value);
            }
        }

        console.log('Gerando relatório com filtros:', Object.fromEntries(params));

        fetch('ajax/generate_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            loadingReport.style.display = 'none';
            
            if (data.success) {
                currentReportData = data.data;
                currentStats = data.stats;
                
                displayStats(data.stats);
                displayResults(data.data);
                
                showToast(`Relatório gerado com ${data.data.length} registro(s)`, 'success');
            } else {
                showError(data.message || 'Erro ao gerar relatório');
            }
        })
        .catch(error => {
            loadingReport.style.display = 'none';
            showError('Erro de conexão: ' + error.message);
            console.error('Erro:', error);
        });
    }

    function displayStats(stats) {
        statsGrid.innerHTML = '';
        
        const statCards = [
            { 
                label: 'Total de Participantes', 
                value: stats.total || 0, 
                icon: 'fas fa-users',
                color: '#4a90e2'
            },
            { 
                label: 'Média de Idade', 
                value: (stats.media_idade || 0) + ' anos', 
                icon: 'fas fa-birthday-cake',
                color: '#10b981'
            },
            { 
                label: 'Bairros Diferentes', 
                value: stats.total_bairros || 0, 
                icon: 'fas fa-map-marker-alt',
                color: '#f59e0b'
            },
            { 
                label: 'Cidades Diferentes', 
                value: stats.total_cidades || 0, 
                icon: 'fas fa-city',
                color: '#8b5cf6'
            },
            { 
                label: 'Inscrições Ativas', 
                value: stats.ativas || 0, 
                icon: 'fas fa-check-circle',
                color: '#10b981'
            },
            { 
                label: 'Inscrições Canceladas', 
                value: stats.canceladas || 0, 
                icon: 'fas fa-times-circle',
                color: '#ef4444'
            }
        ];

        statCards.forEach((stat, index) => {
            const card = document.createElement('div');
            card.className = 'stat-card';
            card.style.animationDelay = `${index * 0.1}s`;
            card.innerHTML = `
                <div class="stat-icon" style="color: ${stat.color}">
                    <i class="${stat.icon}"></i>
                </div>
                <div class="stat-value">${stat.value}</div>
                <div class="stat-label">${stat.label}</div>
            `;
            statsGrid.appendChild(card);
        });
    }

    function displayResults(data) {
        if (!data || data.length === 0) {
            reportTable.style.display = 'none';
            emptyReport.style.display = 'block';
            return;
        }

        reportTableBody.innerHTML = '';
        
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.style.animationDelay = `${index * 0.05}s`;
            tr.innerHTML = `
                <td><span class="id-badge">${row.id_inscricao_formatado}</span></td>
                <td><strong>${row.nome_completo}</strong></td>
                <td>${formatCPF(row.cpf)}</td>
                <td>${row.idade} anos</td>
                <td><a href="mailto:${row.email}" style="color: #4a90e2; text-decoration: none;">${row.email}</a></td>
                <td><a href="tel:${row.telefone}" style="color: #4a90e2; text-decoration: none;">${row.telefone}</a></td>
                <td>${formatCEP(row.cep)}</td>
                <td>${row.logradouro}</td>
                <td>${row.numero}</td>
                <td>${row.complemento || '-'}</td>
                <td>${row.bairro}</td>
                <td>${row.cidade}</td>
                <td>${row.estado}</td>
                <td>${formatDate(row.data_inscricao)}</td>
                <td><span class="status-${row.status}">${capitalizeFirst(row.status)}</span></td>
            `;
            reportTableBody.appendChild(tr);
        });

        reportTable.style.display = 'table';
        emptyReport.style.display = 'none';
    }

    function exportToCSV() {
        if (!currentReportData || currentReportData.length === 0) return;

        const headers = [
            'ID', 'Nome Completo', 'CPF', 'Idade', 'Email', 'Telefone', 
            'CEP', 'Logradouro', 'Número', 'Complemento', 'Bairro', 
            'Cidade', 'Estado', 'Data Inscrição', 'Status'
        ];
        
        let csv = headers.join(',') + '\n';
        
        currentReportData.forEach(row => {
            const csvRow = [
                row.id_inscricao_formatado,
                `"${row.nome_completo.replace(/"/g, '""')}"`, // Escape aspas duplas
                formatCPF(row.cpf),
                row.idade + ' anos',
                row.email,
                row.telefone,
                formatCEP(row.cep),
                `"${row.logradouro.replace(/"/g, '""')}"`,
                row.numero,
                `"${(row.complemento || '').replace(/"/g, '""')}"`,
                `"${row.bairro.replace(/"/g, '""')}"`,
                `"${row.cidade.replace(/"/g, '""')}"`,
                row.estado,
                formatDate(row.data_inscricao),
                capitalizeFirst(row.status)
            ].join(',');
            csv += csvRow + '\n';
        });

        const filename = `relatorio-evento-bike-socorro-${new Date().toISOString().split('T')[0]}.csv`;
        downloadFile(csv, filename, 'text/csv;charset=utf-8;');
        
        showToast('Relatório CSV exportado com sucesso!', 'success');
    }

    function exportToExcel() {
        if (!currentReportData || currentReportData.length === 0) return;

        // Criar tabela HTML para conversão Excel
        let html = `
            <meta charset="UTF-8">
            <table border="1">
                <thead>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <th>ID</th>
                        <th>Nome Completo</th>
                        <th>CPF</th>
                        <th>Idade</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>CEP</th>
                        <th>Logradouro</th>
                        <th>Número</th>
                        <th>Complemento</th>
                        <th>Bairro</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                        <th>Data Inscrição</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
        `;

        currentReportData.forEach(row => {
            html += `
                <tr>
                    <td>${row.id_inscricao_formatado}</td>
                    <td>${row.nome_completo}</td>
                    <td>${formatCPF(row.cpf)}</td>
                    <td>${row.idade} anos</td>
                    <td>${row.email}</td>
                    <td>${row.telefone}</td>
                    <td>${formatCEP(row.cep)}</td>
                    <td>${row.logradouro}</td>
                    <td>${row.numero}</td>
                    <td>${row.complemento || ''}</td>
                    <td>${row.bairro}</td>
                    <td>${row.cidade}</td>
                    <td>${row.estado}</td>
                    <td>${formatDate(row.data_inscricao)}</td>
                    <td>${capitalizeFirst(row.status)}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';

        // Converter para Excel usando data URI
        const excelFile = "data:application/vnd.ms-excel;charset=utf-8," + encodeURIComponent(html);
        const filename = `relatorio-evento-bike-socorro-${new Date().toISOString().split('T')[0]}.xls`;
        
        downloadFile(excelFile, filename, null, true);
        
        showToast('Relatório Excel exportado com sucesso!', 'success');
    }

    function downloadFile(content, filename, mimeType, isDataUri = false) {
        try {
            const blob = isDataUri ? null : new Blob([content], { type: mimeType });
            const url = isDataUri ? content : URL.createObjectURL(blob);
            
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            
            if (!isDataUri && url.startsWith('blob:')) {
                setTimeout(() => URL.revokeObjectURL(url), 100);
            }
        } catch (error) {
            console.error('Erro ao baixar arquivo:', error);
            showToast('Erro ao baixar arquivo', 'error');
        }
    }

    function showError(message) {
        showToast(message, 'error');
        
        // Mostrar área vazia se houver erro
        reportTable.style.display = 'none';
        emptyReport.style.display = 'block';
        
        // Atualizar mensagem de erro
        const emptyIcon = emptyReport.querySelector('i');
        const emptyTitle = emptyReport.querySelector('h3');
        const emptyDesc = emptyReport.querySelector('p');
        
        if (emptyIcon) emptyIcon.className = 'fas fa-exclamation-triangle';
        if (emptyTitle) emptyTitle.textContent = 'Erro ao Gerar Relatório';
        if (emptyDesc) emptyDesc.textContent = message;
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-triangle',
            warning: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        
        toast.innerHTML = `
            <i class="${icons[type]}"></i>
            <span>${message}</span>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        Object.assign(toast.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: colors[type],
            color: 'white',
            padding: '16px 20px',
            borderRadius: '12px',
            display: 'flex',
            alignItems: 'center',
            gap: '12px',
            zIndex: '10001',
            boxShadow: '0 8px 25px rgba(0,0,0,0.15)',
            animation: 'slideInRight 0.3s ease',
            fontWeight: '500',
            maxWidth: '400px',
            fontSize: '0.95rem'
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

    // Funções auxiliares
    function formatCPF(cpf) {
        if (!cpf) return '';
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11) return cpf;
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    function formatCEP(cep) {
        if (!cep) return '';
        cep = cep.replace(/\D/g, '');
        if (cep.length !== 8) return cep;
        return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }) + ' ' + date.toLocaleTimeString('pt-BR', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        } catch (error) {
            return dateString;
        }
    }

    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Funções do modal de impressão
    window.closePrintModal = function() {
        printModal.style.display = 'none';
    };

    window.executePrint = function() {
        const printType = document.querySelector('input[name="printType"]:checked')?.value || 'complete';
        
        let printContent = '';
        const currentDate = new Date().toLocaleDateString('pt-BR');
        const currentTime = new Date().toLocaleTimeString('pt-BR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        switch(printType) {
            case 'complete':
                printContent = generateCompletePrint();
                break;
            case 'summary':
                printContent = generateSummaryPrint();
                break;
            case 'stats':
                printContent = generateStatsPrint();
                break;
            default:
                printContent = generateCompletePrint();
        }

        const printWindow = window.open('', '_blank', 'width=1200,height=800');
        
        if (!printWindow) {
            showToast('Por favor, permita pop-ups para imprimir o relatório', 'warning');
            return;
        }

        printWindow.document.write(`
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
                <meta charset="UTF-8">
                <title>Relatório - Evento Bike Socorro</title>
                <style>
                    body { 
                        font-family: 'Arial', sans-serif; 
                        margin: 20px; 
                        color: #333;
                        line-height: 1.4;
                    }
                    .print-header { 
                        text-align: center; 
                        margin-bottom: 30px; 
                        border-bottom: 3px solid #4a90e2; 
                        padding-bottom: 20px; 
                    }
                    .print-header h1 {
                        color: #4a90e2;
                        margin: 0 0 10px 0;
                        font-size: 1.8em;
                    }
                    .print-header h2 {
                        color: #6b7280;
                        margin: 0;
                        font-weight: normal;
                        font-size: 1.2em;
                    }
                    .print-date { 
                        text-align: right; 
                        margin-bottom: 25px; 
                        color: #6b7280; 
                        font-size: 0.9em;
                    }
                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-bottom: 20px; 
                        font-size: 0.85em;
                    }
                    th, td { 
                        border: 1px solid #ddd; 
                        padding: 8px 6px; 
                        text-align: left; 
                        vertical-align: top;
                    }
                    th { 
                        background-color: #f8fafc; 
                        font-weight: bold; 
                        color: #4a5568;
                        font-size: 0.8em;
                        text-transform: uppercase;
                    }
                    tr:nth-child(even) {
                        background-color: #f9fafb;
                    }
                    .stats-grid { 
                        display: grid; 
                        grid-template-columns: repeat(3, 1fr); 
                        gap: 20px; 
                        margin-bottom: 30px; 
                    }
                    .stat-card { 
                        border: 2px solid #e2e8f0; 
                        padding: 20px; 
                        text-align: center; 
                        border-radius: 8px;
                        background: #f8fafc;
                    }
                    .stat-value { 
                        font-size: 1.8em; 
                        font-weight: bold; 
                        color: #4a90e2; 
                        margin-bottom: 5px;
                    }
                    .stat-label {
                        color: #6b7280;
                        font-size: 0.9em;
                    }
                    .id-badge {
                        background: #e8f2ff;
                        color: #4a90e2;
                        padding: 2px 6px;
                        border-radius: 4px;
                        font-family: 'Courier New', monospace;
                        font-weight: bold;
                    }
                    .status-ativa {
                        background: #dcfce7;
                        color: #166534;
                        padding: 2px 8px;
                        border-radius: 12px;
                        font-size: 0.8em;
                    }
                    .status-cancelada {
                        background: #fef2f2;
                        color: #dc2626;
                        padding: 2px 8px;
                        border-radius: 12px;
                        font-size: 0.8em;
                    }
                    @media print { 
                        body { 
                            margin: 0; 
                            font-size: 12px;
                        }
                        .stats-grid {
                            grid-template-columns: repeat(2, 1fr);
                        }
                        table {
                            font-size: 0.75em;
                        }
                        th, td {
                            padding: 4px 3px;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h1>Relatório - Pedala Socorro 2026</h1>
                    <h2>Data do Evento: 16 de Agosto de 2026</h2>
                </div>
                <div class="print-date">
                    Relatório gerado em: ${currentDate} às ${currentTime}
                </div>
                ${printContent}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        
        // Aguardar carregamento e imprimir
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
            }, 500);
        };
        
        closePrintModal();
        showToast('Relatório enviado para impressão!', 'success');
    };

    function generateCompletePrint() {
        if (!currentReportData || currentReportData.length === 0) {
            return '<p style="text-align: center; color: #6b7280; font-size: 1.2em;">Nenhum dado disponível para impressão</p>';
        }

        let html = `
            <h3 style="color: #4a90e2; margin-bottom: 15px;">Relatório Completo (${currentReportData.length} participante(s))</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>CEP</th>
                        <th>Endereço</th>
                        <th>Bairro</th>
                        <th>Cidade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
        `;

        currentReportData.forEach(row => {
            const endereco = `${row.logradouro}, ${row.numero}${row.complemento ? ', ' + row.complemento : ''}`;
            html += `
                <tr>
                    <td><span class="id-badge">${row.id_inscricao_formatado}</span></td>
                    <td>${row.nome_completo}</td>
                    <td>${formatCPF(row.cpf)}</td>
                    <td>${row.email}</td>
                    <td>${row.telefone}</td>
                    <td>${formatCEP(row.cep)}</td>
                    <td>${endereco}</td>
                    <td>${row.bairro}</td>
                    <td>${row.cidade} - ${row.estado}</td>
                    <td><span class="status-${row.status}">${capitalizeFirst(row.status)}</span></td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        return html;
    }

    function generateSummaryPrint() {
        if (!currentReportData || currentReportData.length === 0) {
            return '<p style="text-align: center; color: #6b7280; font-size: 1.2em;">Nenhum dado disponível para impressão</p>';
        }

        let html = `
            <h3 style="color: #4a90e2; margin-bottom: 15px;">Relatório Resumido (${currentReportData.length} participante(s))</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Bairro</th>
                        <th>Cidade</th>
                    </tr>
                </thead>
                <tbody>
        `;

        currentReportData.forEach(row => {
            html += `
                <tr>
                    <td><span class="id-badge">${row.id_inscricao_formatado}</span></td>
                    <td>${row.nome_completo}</td>
                    <td>${row.telefone}</td>
                    <td>${row.bairro}</td>
                    <td>${row.cidade} - ${row.estado}</td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        return html;
    }

    function generateStatsPrint() {
        if (!currentStats) {
            return '<p style="text-align: center; color: #6b7280; font-size: 1.2em;">Nenhuma estatística disponível para impressão</p>';
        }

        return `
            <h3 style="color: #4a90e2; margin-bottom: 15px;">Estatísticas do Evento</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">${currentStats.total || 0}</div>
                    <div class="stat-label">Total de Participantes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${currentStats.media_idade || 0}</div>
                    <div class="stat-label">Média de Idade (anos)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${currentStats.total_bairros || 0}</div>
                    <div class="stat-label">Bairros Diferentes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${currentStats.ativas || 0}</div>
                    <div class="stat-label">Inscrições Ativas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${currentStats.canceladas || 0}</div>
                    <div class="stat-label">Inscrições Canceladas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${currentStats.total_cidades || 0}</div>
                    <div class="stat-label">Cidades Diferentes</div>
                </div>
            </div>
        `;
    }

    // Fechar modal ao clicar fora
    if (printModal) {
        printModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePrintModal();
            }
        });
    }

    // Adicionar estilos para animações de toast
    if (!document.getElementById('toast-animations')) {
        const style = document.createElement('style');
        style.id = 'toast-animations';
        style.textContent = `
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
            
            .stat-card {
                animation: fadeInUp 0.6s ease-out forwards;
                opacity: 0;
            }
            
            .report-table tr {
                animation: fadeInUp 0.3s ease-out forwards;
                opacity: 0;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .id-badge {
                font-family: 'Courier New', monospace;
                font-weight: bold;
                color: #4a90e2;
                background: #e8f2ff;
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 0.9em;
            }
        `;
        document.head.appendChild(style);
    }
});
