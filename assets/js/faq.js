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

    if (!searchInput) {
        return;
    }

    // Guardar o HTML original de cada pergunta e resposta.
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer-content');

        if (question && !question.dataset.originalHtml) {
            question.dataset.originalHtml = question.innerHTML;
        }

        if (answer && !answer.dataset.originalHtml) {
            answer.dataset.originalHtml = answer.innerHTML;
        }
    });

    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.trim().toLowerCase();
        let visibleCount = 0;

        faqItems.forEach(item => {
            // Restaurar o HTML antes de uma nova busca.
            removeHighlight(item);

            const questionElement = item.querySelector('.faq-question');
            const answerElement = item.querySelector('.faq-answer-content');

            const questionText =
                questionElement?.textContent.toLowerCase() || '';

            const answerText =
                answerElement?.textContent.toLowerCase() || '';

            const found =
                searchTerm === '' ||
                questionText.includes(searchTerm) ||
                answerText.includes(searchTerm);

            if (found) {
                item.style.display = 'block';
                visibleCount++;

                if (searchTerm !== '') {
                    highlightSearchTerm(item, searchTerm);
                }
            } else {
                item.style.display = 'none';
            }
        });

        toggleNoResults(visibleCount === 0 && searchTerm !== '');
    });
}

// Destacar somente textos visíveis, sem pesquisar tags HTML.
function highlightSearchTerm(item, term) {
    const questionText =
        item.querySelector('.faq-question > span') ||
        item.querySelector('.faq-question');

    const answer = item.querySelector('.faq-answer-content');

    [questionText, answer].forEach(element => {
        if (element) {
            highlightTextNodes(element, term);
        }
    });
}

// Percorrer somente nós de texto.
function highlightTextNodes(rootElement, term) {
    const textNodes = [];

    const walker = document.createTreeWalker(
        rootElement,
        NodeFilter.SHOW_TEXT,
        {
            acceptNode(node) {
                const text = node.nodeValue || '';

                if (!text.trim()) {
                    return NodeFilter.FILTER_REJECT;
                }

                // Não alterar o ícone da pergunta.
                if (node.parentElement?.closest('.faq-icon')) {
                    return NodeFilter.FILTER_REJECT;
                }

                return text.toLowerCase().includes(term.toLowerCase())
                    ? NodeFilter.FILTER_ACCEPT
                    : NodeFilter.FILTER_REJECT;
            }
        }
    );

    while (walker.nextNode()) {
        textNodes.push(walker.currentNode);
    }

    const regex = new RegExp(`(${escapeRegExp(term)})`, 'gi');

    textNodes.forEach(textNode => {
        const text = textNode.nodeValue;
        const fragment = document.createDocumentFragment();

        let lastIndex = 0;
        let match;

        regex.lastIndex = 0;

        while ((match = regex.exec(text)) !== null) {
            fragment.appendChild(
                document.createTextNode(
                    text.substring(lastIndex, match.index)
                )
            );

            const highlight = document.createElement('span');
            highlight.className = 'highlight';
            highlight.textContent = match[0];

            fragment.appendChild(highlight);

            lastIndex = regex.lastIndex;
        }

        fragment.appendChild(
            document.createTextNode(text.substring(lastIndex))
        );

        textNode.parentNode.replaceChild(fragment, textNode);
    });
}

// Restaurar o conteúdo original.
function removeHighlight(item) {
    const question = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer-content');

    if (question?.dataset.originalHtml) {
        question.innerHTML = question.dataset.originalHtml;
    }

    if (answer?.dataset.originalHtml) {
        answer.innerHTML = answer.dataset.originalHtml;
    }
}

// Proteger caracteres especiais usados em expressões regulares.
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

                faqItems.forEach(item => {
                    removeHighlight(item);
                });

                toggleNoResults(false);
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
