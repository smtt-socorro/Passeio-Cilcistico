// JavaScript para Gerenciamento de Usuários Admin

function openCreateModal() {
    document.getElementById('userModalTitle').textContent = 'Novo Usuário';
    document.getElementById('formAction').value = 'create';
    document.getElementById('userId').value = '';
    document.getElementById('username').value = '';
    document.getElementById('nome_completo').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('password').required = true;
    document.getElementById('passwordHelp').style.display = 'none';
    document.getElementById('statusGroup').style.display = 'none';
    
    document.getElementById('userModal').style.display = 'flex';
}

function editUser(user) {
    document.getElementById('userModalTitle').textContent = 'Editar Usuário';
    document.getElementById('formAction').value = 'update';
    document.getElementById('userId').value = user.id;
    document.getElementById('username').value = user.username;
    document.getElementById('nome_completo').value = user.nome_completo;
    document.getElementById('email').value = user.email;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('passwordHelp').style.display = 'block';
    document.getElementById('statusGroup').style.display = 'block';
    document.getElementById('status').value = user.status;
    
    document.getElementById('userModal').style.display = 'flex';
}

function deleteUser(id, username) {
    if (confirm(`Tem certeza que deseja desativar o usuário "${username}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function closeUserModal() {
    document.getElementById('userModal').style.display = 'none';
}

// Fechar modal ao clicar fora
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});
