function buscarCEP() {
    const cepInput = document.getElementById('cep');
    
    if (cepInput) {
        cepInput.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            
            if (cep.length === 8) {
                // Limpar campos
                document.getElementById('logradouro').value = '';
                document.getElementById('bairro').value = '';
                document.getElementById('cidade').value = '';
                document.getElementById('estado').value = '';
                
                // Mostrar loading
                this.classList.add('loading');
                
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        this.classList.remove('loading');
                        
                        if (!data.erro) {
                            document.getElementById('logradouro').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                            
                            // Focar no campo número
                            document.getElementById('numero').focus();
                        } else {
                            alert('CEP não encontrado');
                        }
                    })
                    .catch(error => {
                        this.classList.remove('loading');
                        console.error('Erro ao buscar CEP:', error);
                        alert('Erro ao buscar CEP');
                    });
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', buscarCEP);
