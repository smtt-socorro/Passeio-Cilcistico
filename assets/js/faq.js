// JavaScript específico para página FAQ
// Evento Bike SMTT Socorro

document.addEventListener('DOMContentLoaded', function() {
    initFAQPage();
});

function initFAQPage() {
    initAccordion();
    initSearch();
    initCategories();
    initAnimations();
}

// Sistema de accordion
function initAccordion() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.closest('.faq-item');
            const answer = faqItem.querySelector('.faq-answer');
            const isActive = this.classList.contains('active');
            
            // Fechar todas as outras perguntas
            faqQuestions.forEach(q => {
                q.classList.remove('active');
                q.closest('.faq-item').querySelector('.faq-answer').classList.remove('active');
            });
            
            // Abrir/fechar a pergunta clicada
            if (!isActive) {
                this.classList.add('active');
                answer.classList.add('active');
                
                // Scroll suave para a pergunta
                setTimeout(() => {
                    faqItem.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }, 300);
            }
        });
    });
}

// Sistema de busca
function initSearch() {
    const searchInput = document.getElementById('faq-search');
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer-content').textContent.toLowerCase();
                
                if (searchTerm === '' || question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    visibleCount++;
                    
                    // Highlight do termo buscado
                    if (searchTerm !== '') {
                        highlightSearchTerm(item, searchTerm);
                    } else {
                        removeHighlight(item);
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Mostrar/esconder mensagem de "sem resultados"
            toggleNoResults(visibleCount === 0 && searchTerm !== '');
        });
    }
}

// Highlight do termo de busca
function highlightSearchTerm(item, term) {
    const question = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer-content');
    
    [question, answer].forEach(element => {
        const originalText = element.getAttribute('data-original') || element.innerHTML;
        if (!element.getAttribute('data-original')) {
            element.setAttribute('data-original', originalText);
        }
        
        const regex = new RegExp(`(${escapeRegExp(term)})`, 'gi');
        const highlightedText = originalText.replace(regex, '<span class="highlight">$1</span>');
        element.innerHTML = highlightedText;
    });
}

// Remover highlight
function removeHighlight(item) {
    const elements = item.querySelectorAll('[data-original]');
    elements.forEach(element => {
        element.innerHTML = element.getAttribute('data-original');
    });
}

// Escape regex
function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

// Mostrar/esconder "sem resultados"
function toggleNoResults(show) {
    let noResultsDiv = document.querySelector('.no-results');
    
    if (show && !noResultsDiv) {
        noResultsDiv = document.createElement('div');
        noResultsDiv.className = 'no-results';
        noResultsDiv.innerHTML = `
            <div class="no-results-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Nenhum resultado encontrado</h3>
            <p>Tente usar palavras-chave diferentes ou entre em contato conosco.</p>
        `;
        document.querySelector('.faq-list').appendChild(noResultsDiv);
    } else if (!show && noResultsDiv) {
        noResultsDiv.remove();
    }
}

// Sistema de categorias
function initCategories() {
    const categoryBtns = document.querySelectorAll('.category-btn');
    const faqItems = document.querySelectorAll('.faq-item');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const category = this.getAttribute('data-category');
            
            // Atualizar botões ativos
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrar FAQs
            filterByCategory(category, faqItems);
            
            // Limpar busca
            const searchInput = document.getElementById('faq-search');
            if (searchInput) {
                searchInput.value = '';
            }
        });
    });
}

// Filtrar por categoria
function filterByCategory(category, faqItems) {
    faqItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        
        if (category === 'all' || itemCategory === category) {
            item.style.display = 'block';
            item.style.animation = 'fadeInUp 0.5s ease';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Fechar todas as perguntas abertas
    document.querySelectorAll('.faq-question.active').forEach(q => {
        q.classList.remove('active');
        q.closest('.faq-item').querySelector('.faq-answer').classList.remove('active');
    });
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
    document.querySelectorAll('.faq-item, .contact-info-card, .help-card').forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease';
        el.style.transitionDelay = `${index * 0.1}s`;
        observer.observe(el);
    });
}

// Função para abrir FAQ específico via URL
function openFAQFromURL() {
    const hash = window.location.hash;
    if (hash) {
        const targetFAQ = document.querySelector(hash);
        if (targetFAQ) {
            const question = targetFAQ.querySelector('.faq-question');
            if (question) {
                question.click();
                setTimeout(() => {
                    targetFAQ.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 500);
            }
        }
    }
}

// Executar ao carregar
window.addEventListener('load', openFAQFromURL);

// Smooth scroll para links internos
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

// Função para adicionar FAQ dinamicamente (para uso futuro)
function addFAQ(question, answer, category = 'geral') {
    const faqList = document.querySelector('.faq-list');
    const faqId = `faq-${Date.now()}`;
    
    const faqHTML = `
        <div class="faq-item" data-category="${category}" id="${faqId}">
            <button class="faq-question">
                <span>${question}</span>
                <div class="faq-icon">
                    <i class="fas fa-plus"></i>
                </div>
            </button>
            <div class="faq-answer">
                <div class="faq-answer-content">
                    ${answer}
                </div>
            </div>
        </div>
    `;
    
    faqList.insertAdjacentHTML('beforeend', faqHTML);
    
    // Reinicializar accordion para o novo item
    const newItem = document.getElementById(faqId);
    const newQuestion = newItem.querySelector('.faq-question');
    
    newQuestion.addEventListener('click', function() {
        const answer = newItem.querySelector('.faq-answer');
        const isActive = this.classList.contains('active');
        
        // Fechar outras perguntas
        document.querySelectorAll('.faq-question.active').forEach(q => {
            q.classList.remove('active');
            q.closest('.faq-item').querySelector('.faq-answer').classList.remove('active');
        });
        
        // Abrir/fechar a pergunta
        if (!isActive) {
            this.classList.add('active');
            answer.classList.add('active');
        }
    });
    
    return faqId;
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
