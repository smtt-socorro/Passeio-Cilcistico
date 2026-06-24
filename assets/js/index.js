// Index Page - Minimal Flat Design
// Evento Bike SMTT Socorro

document.addEventListener('DOMContentLoaded', function() {
    initMinimalIndex();
});

function initMinimalIndex() {
    initCountdown();
    initScrollAnimations();
    initButtonEffects();
    initDecorations();
}

// Contador regressivo
function initCountdown() {
    const eventDate = new Date('2025-08-29T07:00:00');
    
    function updateCountdown() {
        const now = new Date();
        const timeLeft = eventDate - now;

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            // Atualizar elementos do contador
            updateCountdownElement('days', days);
            updateCountdownElement('hours', hours);
            updateCountdownElement('minutes', minutes);
            updateCountdownElement('seconds', seconds);
        } else {
            // Evento começou
            document.querySelector('.countdown-section').innerHTML = `
                <div class="event-started">
                    <h3 style="color: #10b981; font-size: 24px; font-weight: 700;">🎉 O evento começou!</h3>
                </div>
            `;
        }
    }

    function updateCountdownElement(type, value) {
        const element = document.querySelector(`[data-countdown="${type}"]`);
        if (element) {
            const numberElement = element.querySelector('.countdown-number');
            if (numberElement) {
                const newValue = String(value).padStart(2, '0');
                if (numberElement.textContent !== newValue) {
                    numberElement.style.transform = 'scale(1.1)';
                    numberElement.textContent = newValue;
                    setTimeout(() => {
                        numberElement.style.transform = 'scale(1)';
                    }, 200);
                }
            }
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
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

// Efeitos dos botões
function initButtonEffects() {
    // Ripple effect nos botões
    document.querySelectorAll('.btn-primary, .btn-secondary').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            // Estilos do ripple
            Object.assign(ripple.style, {
                position: 'absolute',
                borderRadius: '50%',
                background: 'rgba(255, 255, 255, 0.6)',
                transform: 'scale(0)',
                animation: 'ripple 0.6s linear',
                pointerEvents: 'none'
            });
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Hover effects para cards
    document.querySelectorAll('.event-info-card, .feature-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Elementos decorativos animados
function initDecorations() {
    const decorations = document.querySelectorAll('.decoration-circle');
    
    decorations.forEach((decoration, index) => {
        // Animação aleatória para cada círculo
        const duration = 6 + (index * 2);
        decoration.style.animationDuration = duration + 's';
        
        // Movimento sutil com mouse
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth) * 10;
            const y = (e.clientY / window.innerHeight) * 10;
            
            decoration.style.transform = `translate(${x}px, ${y}px)`;
        });
    });
}

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

// Adicionar estilos de animação via JavaScript
const animationStyles = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .btn-primary, .btn-secondary {
        position: relative;
        overflow: hidden;
    }
    
    .event-info-card, .feature-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .countdown-number {
        transition: transform 0.2s ease;
    }
`;

// Injetar estilos
if (!document.getElementById('animation-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'animation-styles';
    styleSheet.textContent = animationStyles;
    document.head.appendChild(styleSheet);
}

// Parallax sutil no scroll
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const decorations = document.querySelectorAll('.decoration-circle');
    
    decorations.forEach((decoration, index) => {
        const speed = 0.5 + (index * 0.1);
        decoration.style.transform = `translateY(${scrolled * speed}px)`;
    });
});

// Preloader simples (opcional)
function showPreloader() {
    const preloader = document.createElement('div');
    preloader.id = 'preloader';
    preloader.innerHTML = `
        <div style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        ">
            <div style="
                width: 40px;
                height: 40px;
                border: 3px solid #e2e8f0;
                border-top: 3px solid #3b82f6;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            "></div>
        </div>
    `;
    
    document.body.appendChild(preloader);
    
    // Remover preloader após carregamento
    window.addEventListener('load', () => {
        setTimeout(() => {
            preloader.style.opacity = '0';
            setTimeout(() => {
                preloader.remove();
            }, 500);
        }, 500);
    });
}

// Adicionar estilo do spinner
const spinnerStyle = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;

const spinnerStyleSheet = document.createElement('style');
spinnerStyleSheet.textContent = spinnerStyle;
document.head.appendChild(spinnerStyleSheet);
