// JavaScript específico para página de verificação
// Evento Bike SMTT Socorro

document.addEventListener('DOMContentLoaded', function() {
    initVerificationPage();
});

function initVerificationPage() {
    initCPFMask();
    initCPFValidation();
    initSearchForm();
    initAnimations();
}

// Máscara para CPF
function initCPFMask() {
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})/, '$1-$2');
            e.target.value = value;
        });
    }
}

// Validação de CPF
function initCPFValidation() {
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('blur', function() {
            const cpf = this.value.replace(/\D/g, '');
            const errorDiv = document.querySelector('.field-error');
            
            if (cpf && !validarCPF(cpf)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                errorDiv.textContent = 'CPF inválido';
                errorDiv.classList.add('show');
            } else if (cpf && validarCPF(cpf)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                errorDiv.classList.remove('show');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
                errorDiv.classList.remove('show');
            }
        });
    }
}

// Validação matemática de CPF
function validarCPF(cpf) {
    // Remove caracteres não numéricos
    cpf = cpf.replace(/\D/g, '');
    
    // Verifica se tem 11 dígitos
    if (cpf.length !== 11) {
        return false;
    }
    
    // Verifica se todos os dígitos são iguais
    if (/^(\d)\1{10}$/.test(cpf)) {
        return false;
    }
    
    // Calcula primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let resto = (soma * 10) % 11;
    let dv1 = (resto === 10 || resto === 11) ? 0 : resto;
    
    // Verifica primeiro dígito
    if (parseInt(cpf.charAt(9)) !== dv1) {
        return false;
    }
    
    // Calcula segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    resto = (soma * 10) % 11;
    let dv2 = (resto === 10 || resto === 11) ? 0 : resto;
    
    // Verifica segundo dígito
    return parseInt(cpf.charAt(10)) === dv2;
}

// Controle do formulário de busca
function initSearchForm() {
    const form = document.getElementById('searchForm');
    const searchButton = document.getElementById('searchButton');
    
    if (form && searchButton) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const cpfInput = document.getElementById('cpf');
            const cpf = cpfInput.value.replace(/\D/g, '');
            
            // Validações
            if (!cpf) {
                showError('Por favor, digite um CPF');
                return;
            }
            
            if (!validarCPF(cpf)) {
                showError('CPF inválido. Verifique os dados informados');
                return;
            }
            
            // Iniciar busca
            performSearch(cpf);
        });
    }
}

// Realizar busca
function performSearch(cpf) {
    const searchButton = document.getElementById('searchButton');
    const resultsSection = document.querySelector('.results-section');
    
    // Loading state
    searchButton.classList.add('loading');
    searchButton.disabled = true;
    
    // Limpar resultados anteriores
    if (resultsSection) {
        resultsSection.remove();
    }
    
    // Fazer requisição AJAX
    fetch('verificar_inscricao.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cpf=' + encodeURIComponent(cpf) + '&ajax=1'
    })
    .then(response => response.json())
    .then(data => {
        searchButton.classList.remove('loading');
        searchButton.disabled = false;
        
        if (data.success) {
            showResults(data.inscricoes);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        searchButton.classList.remove('loading');
        searchButton.disabled = false;
        console.error('Erro:', error);
        showError('Erro ao buscar inscrições. Tente novamente.');
    });
}

// Mostrar resultados
function showResults(inscricoes) {
    const container = document.querySelector('.verification-container');
    
    const resultsHTML = `
        <div class="results-section">
            <div class="results-card">
                <div class="results-header">
                    <h2 class="results-title">📋 Inscrições Encontradas</h2>
                    <p class="results-subtitle">${inscricoes.length} inscrição(ões) ativa(s) encontrada(s)</p>
                </div>
                
                ${inscricoes.map((inscricao, index) => `
                    <div style="${index > 0 ? 'border-top: 2px solid #e8f2ff; padding-top: 30px; margin-top: 30px;' : ''}">
                        <div class="inscription-id-highlight">
                            <div class="id-label">ID da Inscrição</div>
                            <div class="id-value">${inscricao.id_inscricao_formatado}</div>
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-section">
                                <h3><i class="fas fa-user"></i> Dados Pessoais</h3>
                                <div class="info-item">
                                    <span class="info-label">Nome:</span>
                                    <span class="info-value">${inscricao.nome_completo}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">CPF:</span>
                                    <span class="info-value">${formatarCPF(inscricao.cpf)}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Email:</span>
                                    <span class="info-value">${inscricao.email}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Telefone:</span>
                                    <span class="info-value">${inscricao.telefone}</span>
                                </div>
                            </div>
                            
                            <div class="info-section">
                                <h3><i class="fas fa-map-marker-alt"></i> Endereço</h3>
                                <div class="info-item">
                                    <span class="info-label">CEP:</span>
                                    <span class="info-value">${formatarCEP(inscricao.cep)}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Logradouro:</span>
                                    <span class="info-value">${inscricao.logradouro}, ${inscricao.numero}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Bairro:</span>
                                    <span class="info-value">${inscricao.bairro}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Cidade:</span>
                                    <span class="info-value">${inscricao.cidade} - ${inscricao.estado}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: #fff9c4; padding: 20px; border-radius: 12px; margin-top: 20px;">
                            <h4 style="color: #4a90e2; margin-bottom: 10px;">
                                <i class="fas fa-info-circle"></i> Informações Importantes
                            </h4>
                            <p style="margin: 0; color: #1a202c; line-height: 1.5;">
                                <strong>Data do Evento:</strong> 16 de Agosto de 2026<br>
                                <strong>Local:</strong> Na Praça Eu Amo Socorro - SE<br>
                                <strong>Retirada do Material:</strong> SMTT de Socorro com CPF e ID de inscrição
                            </p>
                        </div>
                    </div>
                `).join('')}
                
                <a href="index.php" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Voltar à Página Inicial
                </a>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', resultsHTML);
    
    // Scroll suave para os resultados
    setTimeout(() => {
        document.querySelector('.results-section').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
}

// Mostrar erro
function showError(message) {
    const container = document.querySelector('.verification-container');
    
    // Remover resultados anteriores
    const existingResults = document.querySelector('.results-section');
    if (existingResults) {
        existingResults.remove();
    }
    
    const errorHTML = `
        <div class="results-section">
            <div class="error-message">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="error-title">Nenhuma inscrição encontrada</h3>
                <p class="error-text">${message}</p>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #fecaca;">
                    <p style="margin: 0; color: #7f1d1d; font-size: 0.95rem;">
                        <strong>Precisa se inscrever?</strong> 
                        <a href="inscricao.php" style="color: #4a90e2; text-decoration: none;">
                            Clique aqui para fazer sua inscrição
                        </a>
                    </p>
                </div>
                
                <a href="index.php" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                    Voltar à Página Inicial
                </a>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', errorHTML);
    
    // Scroll suave para o erro
    setTimeout(() => {
        document.querySelector('.results-section').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
}

// Funções auxiliares de formatação
function formatarCPF(cpf) {
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

function formatarCEP(cep) {
    return cep.replace(/(\d{5})(\d{3})/, '$1-$2');
}

// Animações de entrada
function initAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observar elementos animáveis
    document.querySelectorAll('.search-column, .contact-info-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
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
