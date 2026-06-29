// JavaScript específico para página de inscrição
// Evento Bike SMTT Socorro

document.addEventListener('DOMContentLoaded', function() {
    initInscricaoForm();
});

function initInscricaoForm() {
    initFormMasks();
    initFormValidations();
    initViaCEP();
    initCPFVerification();
    initFormSubmit();
    initDateMask();
}

// Máscaras de entrada
function initFormMasks() {
    // Máscara CPF
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

    // Máscara Telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }

    // Máscara CEP
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }
}

// Validações de formulário
function initFormValidations() {
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('blur', function() {
            const cpf = this.value.replace(/\D/g, '');
            const errorDiv = document.getElementById('cpf-error');
            
            if (cpf && !validarCPF(cpf)) {
                this.classList.add('is-invalid');
                errorDiv.textContent = 'CPF inválido';
                errorDiv.style.display = 'block';
            } else if (cpf && validarCPF(cpf)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                errorDiv.style.display = 'none';
                
                // Verificar se CPF já existe no banco
                checkCPFExists(cpf);
            } else {
                this.classList.remove('is-invalid', 'is-valid');
                errorDiv.style.display = 'none';
            }
        });
    }

    // Validação email
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else if (this.value) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    }
}

// Verificação de CPF no banco de dados
function initCPFVerification() {
    // Esta função será chamada quando o CPF for validado
}

function checkCPFExists(cpf) {
    // Fazer requisição AJAX para verificar CPF
    fetch('check_cpf.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cpf=' + encodeURIComponent(cpf)
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            if (data.count >= 2) {
                showErrorModal(
                    'Limite de Inscrições Excedido',
                    'Este CPF já possui o máximo de 1 inscrições ativas permitidas.',
                    data.ids
                );
            } else {
                showErrorModal(
                    'CPF Já Cadastrado',
                    'Este CPF já possui uma inscrição ativa no evento. Você  não pode fazer uma segunda inscrição se desejar.',
                    data.ids
                );
            }
        }
    })
    .catch(error => {
        console.error('Erro ao verificar CPF:', error);
    });
}

// Integração ViaCEP
function initViaCEP() {
    const cepInput = document.getElementById('cep');
    const loading = document.getElementById('cep-loading');
    
    if (cepInput && loading) {
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            
            if (cep.length === 8) {
                loading.style.display = 'block';
                
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        loading.style.display = 'none';
                        
                        if (!data.erro) {
                            document.getElementById('logradouro').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                            document.getElementById('numero').focus();
                        } else {
                            showToast('CEP não encontrado', 'error');
                        }
                    })
                    .catch(error => {
                        loading.style.display = 'none';
                        console.error('Erro ao buscar CEP:', error);
                        showToast('Erro ao buscar CEP', 'error');
                    });
            }
        });
    }
}

// Controle do envio do formulário
function initFormSubmit() {
    const form = document.getElementById('inscricaoForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Validações finais
            const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
            if (!validarCPF(cpf)) {
                e.preventDefault();
                showToast('CPF inválido', 'error');
                return false;
            }
            
            const termos = document.getElementById('aceita_termos');
            if (!termos.checked) {
                e.preventDefault();
                showToast('Você deve aceitar os termos de responsabilidade', 'error');
                return false;
            }
            
            // Mostrar loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }, 500);
        });
    }
}

// Funções do Modal de Sucesso
function showSuccessModal(inscricaoId) {
    document.getElementById('loadingOverlay').style.display = 'none';
    document.getElementById('inscricao-id-modal').textContent = inscricaoId;
    
    const modal = document.getElementById('successModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('successModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Funções do Modal de Erro
function showErrorModal(title, message, registrationIds) {
    document.getElementById('loadingOverlay').style.display = 'none';
    
    document.getElementById('error-modal-title').textContent = title;
    document.getElementById('error-modal-message').textContent = message;
    
    if (registrationIds && registrationIds.length > 0) {
        const idsArray = Array.isArray(registrationIds) ? registrationIds : registrationIds.split(',');
        const idsHtml = idsArray.map(id => 
            `<span style="background: var(--accent-blue); color: white; padding: 4px 8px; border-radius: 6px; margin-right: 8px;">${id.trim()}</span>`
        ).join('');
        document.getElementById('registration-ids').innerHTML = idsHtml;
        document.getElementById('existing-registrations').style.display = 'block';
    } else {
        document.getElementById('existing-registrations').style.display = 'none';
    }
    
    const modal = document.getElementById('errorModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeErrorModal() {
    const modal = document.getElementById('errorModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
    
    // Resetar formulário
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-bicycle"></i> Realizar Inscrição';
        submitBtn.disabled = false;
    }
}

// Função para mostrar toast notifications
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${getToastIcon(type)}"></i>
        <span>${message}</span>
    `;
    
    // Estilos do toast
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
        zIndex: '10001',
        boxShadow: '0 8px 25px rgba(0,0,0,0.15)',
        animation: 'slideInRight 0.3s ease',
        fontWeight: '500'
    });

    document.body.appendChild(toast);

    // Remover toast após 4 segundos
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

// Event listeners dos modais
document.addEventListener('DOMContentLoaded', function() {
    // Modal de sucesso
    const successModalClose = document.querySelector('#successModal .modal-close');
    if (successModalClose) {
        successModalClose.addEventListener('click', closeModal);
    }
    
    const successModal = document.getElementById('successModal');
    if (successModal) {
        successModal.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    }
    
    // Modal de erro
    const errorModalButtons = document.querySelectorAll('[onclick="closeErrorModal()"]');
    errorModalButtons.forEach(btn => {
        btn.addEventListener('click', closeErrorModal);
    });
    
    // Fechar modais com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (successModal && successModal.classList.contains('show')) {
                closeModal();
            }
            const errorModal = document.getElementById('errorModal');
            if (errorModal && errorModal.classList.contains('show')) {
                closeErrorModal();
            }
        }
    });
});

// Adicionar estilos de animação do toast
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
`;

// Injetar estilos se ainda não existirem
if (!document.getElementById('toast-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'toast-styles';
    styleSheet.textContent = toastStyles;
    document.head.appendChild(styleSheet);
}

// Máscara para data de nascimento dd/mm/yyyy
function initDateMask() {
    const dateInput = document.getElementById('data_nascimento');
    if (dateInput) {
        dateInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 8);
            
            if (value.length >= 5) {
                e.target.value = value.slice(0, 2) + '/' + value.slice(2, 4) + '/' + value.slice(4, 8);
            } else if (value.length >= 3) {
                e.target.value = value.slice(0, 2) + '/' + value.slice(2, 4);
            } else if (value.length >= 1) {
                e.target.value = value;
            }
        });

        // Validação básica de data
        dateInput.addEventListener('blur', function(e) {
            const dateValue = e.target.value;
            if (dateValue && !isValidDate(dateValue)) {
                e.target.classList.add('is-invalid');
                showToast('Data inválida. Use o formato dd/mm/aaaa', 'error');
            } else {
                e.target.classList.remove('is-invalid');
            }
        });
    }
}

// Função para validar data
function isValidDate(dateString) {
    const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    const match = dateString.match(regex);
    
    if (!match) return false;
    
    const day = parseInt(match[1], 10);
    const month = parseInt(match[2], 10);
    const year = parseInt(match[3], 10);
    
    // Verificar se é uma data válida
    const date = new Date(year, month - 1, day);
    return date.getDate() === day && 
           date.getMonth() === month - 1 && 
           date.getFullYear() === year &&
           year >= 1900 && year <= new Date().getFullYear();
}
