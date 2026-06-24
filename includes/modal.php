<!-- Modal de Sucesso Universal -->
<div id="successModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-check-circle" style="color: var(--success); margin-right: 10px;"></i>
                <span id="success-modal-title">Operação Realizada com Sucesso! 🎉</span>
            </h3>
            <button class="modal-close" data-modal="successModal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="success-modal-content">
                <!-- Conteúdo dinâmico -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" data-modal="successModal">Fechar</button>
        </div>
    </div>
</div>

<!-- Modal de Erro Universal -->
<div id="errorModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-exclamation-triangle" style="color: var(--danger); margin-right: 10px;"></i>
                Atenção
            </h3>
            <button class="modal-close" data-modal="errorModal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="error-modal-content">
                <!-- Conteúdo dinâmico -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal="errorModal">Fechar</button>
        </div>
    </div>
</div>

<!-- Modal de Confirmação Universal -->
<div id="confirmModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-question-circle" style="color: var(--warning); margin-right: 10px;"></i>
                Confirmação
            </h3>
            <button class="modal-close" data-modal="confirmModal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="confirm-modal-content">
                Tem certeza que deseja realizar esta ação?
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-modal="confirmModal">Cancelar</button>
            <button class="btn btn-primary" id="confirm-modal-action">Confirmar</button>
        </div>
    </div>
</div>

<script>
// Controles de Modal Universal
function showModal(modalId, title = '', content = '') {
    const modal = document.getElementById(modalId);
    if (title) {
        const titleElement = modal.querySelector('.modal-title span');
        if (titleElement) titleElement.textContent = title;
    }
    if (content) {
        const contentElement = modal.querySelector(`#${modalId.replace('Modal', '')}-modal-content`);
        if (contentElement) contentElement.innerHTML = content;
    }
    modal.style.display = 'block';
}

function hideModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Event Listeners para fechar modais
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-close') || e.target.hasAttribute('data-modal')) {
        const modalId = e.target.getAttribute('data-modal');
        if (modalId) {
            hideModal(modalId);
        }
    }
    
    // Fechar ao clicar fora do modal
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});

// Funções de conveniência
function showSuccess(title, content) {
    showModal('successModal', title, content);
}

function showError(content) {
    showModal('errorModal', 'Erro', content);
}

function showConfirm(content, callback) {
    showModal('confirmModal', 'Confirmação', content);
    document.getElementById('confirm-modal-action').onclick = function() {
        hideModal('confirmModal');
        if (callback) callback();
    };
}
</script>
