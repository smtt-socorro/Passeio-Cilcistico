// admin/assets/js/certificados.js - Versão Simplificada

document.addEventListener('DOMContentLoaded', function() {
    const buscaInput = document.getElementById('buscaParticipante');
    const suggestionsList = document.getElementById('suggestionsList');
    const certificateActionArea = document.getElementById('certificateActionArea');
    const noParticipantSelected = document.getElementById('noParticipantSelected');
    
    // REMOVIDO: cardParticipantName (nome não é mais exibido)
    const cardIdPrefix = document.getElementById('cardIdPrefix'); 
    const cardIdNumber = document.getElementById('cardIdNumber');
    
    const btnPrintCertificate = document.getElementById('btnPrintCertificate');
    
    let selectedParticipant = null;
    let searchTimeout = null;

    // Verificar se todos os elementos necessários existem
    if (!buscaInput || !suggestionsList || !certificateActionArea || !noParticipantSelected || 
        !cardIdPrefix || !cardIdNumber || !btnPrintCertificate) {
        console.error('Alguns elementos necessários não foram encontrados na página');
        return;
    }

    // Event listener para busca com debounce
    buscaInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        if (searchTerm.length < 2) {
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
            
            if (searchTerm.length === 0) {
                selectedParticipant = null;
                loadCertificatePreview();
            }
            return;
        }

        searchTimeout = setTimeout(function() {
            performSearch(searchTerm);
        }, 300);
    });

    function performSearch(searchTerm) {
        console.log("Buscando por:", searchTerm);

        suggestionsList.innerHTML = '<li style="text-align: center; color: #6b7280; pointer-events: none;">Buscando...</li>';
        suggestionsList.style.display = 'block';

        fetch('ajax/busca_participantes.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'term=' + encodeURIComponent(searchTerm)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log("Resposta AJAX:", data);
            displaySearchResults(data, searchTerm);
        })
        .catch(error => {
            console.error('Erro na busca AJAX:', error);
            suggestionsList.innerHTML = '<li style="text-align: center; color: #ef4444; pointer-events: none;">Erro ao buscar participantes</li>';
            suggestionsList.style.display = 'block';
        });
    }

    function displaySearchResults(data, searchTerm) {
        suggestionsList.innerHTML = '';

        if (data.success && data.participants && data.participants.length > 0) {
            const ul = document.createElement('ul');
            ul.style.listStyle = 'none';
            ul.style.padding = '0';
            ul.style.margin = '0';

            data.participants.forEach(function(participant) {
                const li = document.createElement('li');
                li.style.padding = '12px 20px';
                li.style.cursor = 'pointer';
                li.style.borderBottom = '1px solid #f1f5f9';
                li.style.transition = 'background-color 0.2s ease';

                const participantName = participant.nome_completo || 'Nome não disponível';
                const participantId = participant.id_inscricao_formatado || 'ID não disponível';
                const participantCpf = participant.cpf ? formatCPF(participant.cpf) : 'CPF não disponível';

                li.innerHTML = '<strong>' + escapeHtml(participantName) + '</strong> <small style="color: #6b7280;">(ID: ' + escapeHtml(participantId) + ', CPF: ' + escapeHtml(participantCpf) + ')</small>';
                
                li.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#e8f2ff';
                });

                li.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent';
                });
                
                li.addEventListener('click', function() {
                    console.log("Participante clicado:", participant);
                    selectParticipant(participant);
                });

                ul.appendChild(li);
            });

            suggestionsList.appendChild(ul);
            suggestionsList.style.display = 'block';
        } else {
            const li = document.createElement('li');
            li.style.textAlign = 'center';
            li.style.color = '#6b7280';
            li.style.pointerEvents = 'none';
            li.style.padding = '12px 20px';
            li.textContent = 'Nenhum participante encontrado para "' + searchTerm + '"';
            
            suggestionsList.appendChild(li);
            suggestionsList.style.display = 'block';
        }
    }

    function selectParticipant(participant) {
        selectedParticipant = participant;
        buscaInput.value = participant.nome_completo + ' (ID: ' + participant.id_inscricao_formatado + ')';
        suggestionsList.innerHTML = '';
        suggestionsList.style.display = 'none';
        loadCertificatePreview();
    }

    document.addEventListener('click', function(e) {
        if (!suggestionsList.contains(e.target) && e.target !== buscaInput) {
            suggestionsList.style.display = 'none';
        }
    });

    function loadCertificatePreview() {
        console.log("loadCertificatePreview chamado. selectedParticipant:", selectedParticipant);
        
        if (selectedParticipant && selectedParticipant.id_inscricao_formatado) {
            // REMOVIDO: Atualização do nome do participante
            
            // Atualizar apenas o ID de inscrição
            const idFormatado = selectedParticipant.id_inscricao_formatado;
            if (idFormatado && idFormatado.length > 1 && idFormatado.toUpperCase().startsWith('S')) {
                cardIdPrefix.textContent = 'S';
                cardIdNumber.textContent = idFormatado.substring(1);
            } else {
                cardIdPrefix.textContent = '';
                cardIdNumber.textContent = idFormatado || 'N/A';
            }
            
            certificateActionArea.style.display = 'block';
            noParticipantSelected.style.display = 'none';
            
            console.log("Preview carregado para ID:", selectedParticipant.id_inscricao_formatado);
        } else {
            certificateActionArea.style.display = 'none';
            noParticipantSelected.style.display = 'block';
            console.log("Nenhum participante selecionado para preview.");
        }
    }

    btnPrintCertificate.addEventListener('click', function() {
        if (!selectedParticipant) {
            alert('Por favor, selecione um participante válido primeiro.');
            return;
        }

        printCertificate();
    });

    function printCertificate() {
        const certificateContent = document.getElementById('certificateContent');
        if (!certificateContent) {
            alert('Erro: Conteúdo do certificado não encontrado.');
            return;
        }

        const certificateHTML = certificateContent.outerHTML;
        const participantId = selectedParticipant.id_inscricao_formatado;
        
        const printWindow = window.open('', '_blank', 'width=1122,height=793');
        
        if (!printWindow) {
            alert('Por favor, permita pop-ups para imprimir o cartão.');
            return;
        }

        // HTML da página de impressão com configurações de cor
        const printHTML = `
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cartão de Inscrição - ${escapeHtml(participantId)}</title>
    <link rel="stylesheet" href="assets/css/certificados.css">
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            background: #ffffff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        .certificate-print-area { 
            width: 297mm; 
            height: 210mm; 
            overflow: hidden; 
        }
        .certificate-a4-horizontal.card-design { 
            transform: scale(1) !important; 
            margin: 0 !important; 
            border: none !important;
            box-shadow: none !important; 
            width: 100%; 
            height: 100%;
        }
        .card-footer {
            background-color: #0d47a1 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        @media print {
            @page { 
                size: A4 landscape; 
                margin: 0mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
            }
            body {
                height: 210mm;
                width: 297mm;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-print-area">
        ${certificateHTML}
    </div>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>`;

        printWindow.document.write(printHTML);
        printWindow.document.close();
    }

    // Funções auxiliares
    function formatCPF(cpf) {
        if (!cpf) return '';
        cpf = cpf.replace(/\D/g, '');
        if (cpf.length !== 11) return cpf;
        return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    loadCertificatePreview();
});
