// Modal controls
function initModals() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        const closeBtn = modal.querySelector('.modal-close');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }
        
        // Fechar ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
}

// Validação do formulário
function initFormValidation() {
    const form = document.getElementById('inscricaoForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const cpfInput = document.getElementById('cpf');
            const cpf = cpfInput.value.replace(/\D/g, '');
            
            if (!validarCPF(cpf)) {
                e.preventDefault();
                cpfInput.focus();
                alert('Por favor, insira um CPF válido');
                return false;
            }
            
            const termosCheck = document.querySelector('input[name="aceita_termos"]');
            if (!termosCheck.checked) {
                e.preventDefault();
                alert('Você deve aceitar os termos de responsabilidade');
                return false;
            }
        });
    }
}

// Animações nos botões
function initButtonAnimations() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initModals();
    initFormValidation();
    initButtonAnimations();
});
