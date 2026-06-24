// JavaScript específico para página de termos
// Evento Bike SMTT Socorro

document.addEventListener('DOMContentLoaded', function() {
    initTermosPage();
});

function initTermosPage() {
    initScrollAnimations();
    initAcceptanceLogic();
    initSmoothScrolling();
}

// Animações de scroll
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observar elementos com animação
    document.querySelectorAll('.scroll-reveal').forEach(el => {
        observer.observe(el);
    });
}

// Lógica de aceitação dos termos
function initAcceptanceLogic() {
    const checkbox = document.getElementById('aceitar-termos');
    const btnAceitar = document.getElementById('btn-aceitar');
    
    if (checkbox && btnAceitar) {
        // Inicialmente desabilitado
        btnAceitar.disabled = true;
        
        checkbox.addEventListener('change', function() {
            btnAceitar.disabled = !this.checked;
            
            if (this.checked) {
                btnAceitar.style.opacity = '1';
                btnAceitar.style.cursor = 'pointer';
            } else {
                btnAceitar.style.opacity = '0.5';
                btnAceitar.style.cursor = 'not-allowed';
            }
        });
        
        btnAceitar.addEventListener('click', function(e) {
            if (!checkbox.checked) {
                e.preventDefault();
                showToast('Você deve aceitar os termos para continuar', 'warning');
                return false;
            }
            
            // Salvar aceitação no localStorage
            localStorage.setItem('termos_aceitos', 'true');
            localStorage.setItem('termos_aceitos_data', new Date().toISOString());
            
            showToast('Termos aceitos com sucesso!', 'success');
            
            // Redirecionar para inscrição após 1 segundo
            setTimeout(() => {
                window.location.href = 'inscricao.php';
            }, 1000);
        });
    }
}

// Scroll suave para âncoras
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Função para destacar seção específica via URL
function highlightSection() {
    const hash = window.location.hash;
    if (hash) {
        const section = document.querySelector(hash);
        if (section) {
            section.style.background = '#e8f2ff';
            section.style.borderRadius = '12px';
            section.style.padding = '20px';
            section.style.margin = '20px 0';
            section.style.border = '2px solid #4a90e2';
            
            setTimeout(() => {
                section.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        }
    }
}

// Executar ao carregar
window.addEventListener('load', highlightSection);

// Função para verificar se os termos foram aceitos
function verificarTermosAceitos() {
    const termosAceitos = localStorage.getItem('termos_aceitos');
    const dataAceitacao = localStorage.getItem('termos_aceitos_data');
    
    if (termosAceitos === 'true' && dataAceitacao) {
        const dataAceite = new Date(dataAceitacao);
        const agora = new Date();
        const diasDiferenca = (agora - dataAceite) / (1000 * 60 * 60 * 24);
        
        // Termos válidos por 30 dias
        if (diasDiferenca < 30) {
            return true;
        }
    }
    
    return false;
}

// Toast notifications
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${getToastIcon(type)}"></i>
        <span>${message}</span>
    `;
    
    Object.assign(toast.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        background: getToastColor(type),
        color: 'white',
        padding: '15px 20px',
        borderRadius: '12px',
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
        zIndex: '10000',
        boxShadow: '0 8px 25px rgba(0,0,0,0.15)',
        animation: 'slideInRight 0.3s ease',
        fontWeight: '500'
    });

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 4000);
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

// Adicionar estilos de animação via JavaScript
const animationStyles = `
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
`;

// Injetar estilos
if (!document.getElementById('termos-animation-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'termos-animation-styles';
    styleSheet.textContent = animationStyles;
    document.head.appendChild(styleSheet);
}

// Função para imprimir os termos
function imprimirTermos() {
    const conteudo = document.querySelector('.termos-content').innerHTML;
    const janela = window.open('', '_blank');
    
    janela.document.write(`
        <html>
            <head>
                <title>Termos de Responsabilidade - Evento Bike Socorro</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                    h1, h2, h3 { color: #1a202c; }
                    .destaque-importante, .destaque-seguranca, .destaque-evento { 
                        padding: 15px; margin: 20px 0; border-radius: 8px; 
                    }
                    .destaque-importante { background: #fff9c4; border: 1px solid #ffd700; }
                    .destaque-seguranca { background: #fef2f2; border: 1px solid #fecaca; }
                    .destaque-evento { background: #e8f2ff; border: 1px solid #bfdbfe; }
                    @media print { body { margin: 20px; } }
                </style>
            </head>
            <body>
                <h1>Termos de Responsabilidade - Evento de Bicicleta Socorro</h1>
                ${conteudo}
            </body>
        </html>
    `);
    
    janela.document.close();
    janela.print();
}

// Adicionar botão de impressão se necessário
function adicionarBotaoImpressao() {
    const container = document.querySelector('.termos-container');
    if (container) {
        const btnImprimir = document.createElement('button');
        btnImprimir.innerHTML = '<i class="fas fa-print"></i> Imprimir Termos';
        btnImprimir.className = 'back-button';
        btnImprimir.style.marginLeft = '10px';
        btnImprimir.onclick = imprimirTermos;
        
        const backButton = document.querySelector('.back-button');
        if (backButton) {
            backButton.parentNode.insertBefore(btnImprimir, backButton.nextSibling);
        }
    }
}

// Executar após carregamento
setTimeout(adicionarBotaoImpressao, 1000);
