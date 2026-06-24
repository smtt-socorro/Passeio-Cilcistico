// JavaScript específico para Login Admin
// Evento Bike SMTT Socorro

document.addEventListener("DOMContentLoaded", function () {
  initLoginPage();
});

function initLoginPage() {
  initFormValidation();
  initPasswordToggle();
  initFormSubmit();
  initKeyboardShortcuts();
  initAnimations();
}

// Validação do formulário
function initFormValidation() {
  const form = document.getElementById("loginForm");
  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

  if (usernameInput) {
    usernameInput.addEventListener("blur", function () {
      validateUsername(this);
    });

    usernameInput.addEventListener("input", function () {
      clearValidationState(this);
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener("blur", function () {
      validatePassword(this);
    });

    passwordInput.addEventListener("input", function () {
      clearValidationState(this);
    });
  }
}

// Validar username
function validateUsername(input) {
  const value = input.value.trim();
  const errorElement = input.parentElement.querySelector(".field-error");

  if (!value) {
    showFieldError(input, errorElement, "Nome de usuário é obrigatório");
    return false;
  }

  if (value.length < 3) {
    showFieldError(
      input,
      errorElement,
      "Nome de usuário deve ter pelo menos 3 caracteres"
    );
    return false;
  }

  showFieldSuccess(input, errorElement);
  return true;
}

// Validar password
function validatePassword(input) {
  const value = input.value;
  const errorElement = input.parentElement.querySelector(".field-error");

  if (!value) {
    showFieldError(input, errorElement, "Senha é obrigatória");
    return false;
  }

  if (value.length < 6) {
    showFieldError(
      input,
      errorElement,
      "Senha deve ter pelo menos 6 caracteres"
    );
    return false;
  }

  showFieldSuccess(input, errorElement);
  return true;
}

// Mostrar erro de campo
function showFieldError(input, errorElement, message) {
  input.classList.add("is-invalid");
  input.classList.remove("is-valid");

  if (errorElement) {
    errorElement.textContent = message;
    errorElement.classList.add("show");
  }
}

// Mostrar sucesso de campo
function showFieldSuccess(input, errorElement) {
  input.classList.remove("is-invalid");
  input.classList.add("is-valid");

  if (errorElement) {
    errorElement.classList.remove("show");
  }
}

// Limpar estado de validação
function clearValidationState(input) {
  input.classList.remove("is-invalid", "is-valid");
  const errorElement = input.parentElement.querySelector(".field-error");
  if (errorElement) {
    errorElement.classList.remove("show");
  }
}

// Toggle de mostrar/ocultar senha
function initPasswordToggle() {
  const passwordInput = document.getElementById("password");
  const toggleButton = document.querySelector(".password-toggle");

  if (passwordInput && toggleButton) {
    toggleButton.addEventListener("click", function () {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      const icon = this.querySelector("i");
      if (type === "password") {
        icon.className = "fas fa-eye";
        this.setAttribute("title", "Mostrar senha");
      } else {
        icon.className = "fas fa-eye-slash";
        this.setAttribute("title", "Ocultar senha");
      }
    });
  }
}

// Controle do envio do formulário
function initFormSubmit() {
  const form = document.getElementById("loginForm");
  const submitButton = document.getElementById("loginButton");

  if (form && submitButton) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      // Validar todos os campos
      const usernameValid = validateUsername(
        document.getElementById("username")
      );
      const passwordValid = validatePassword(
        document.getElementById("password")
      );

      if (!usernameValid || !passwordValid) {
        showToast("Por favor, corrija os erros no formulário", "error");
        return;
      }

      // Mostrar loading
      submitButton.classList.add("loading");
      submitButton.disabled = true;

      // Simular delay de rede
      setTimeout(() => {
        // Enviar formulário
        this.submit();
      }, 1000);
    });
  }
}

// Atalhos de teclado
function initKeyboardShortcuts() {
  document.addEventListener("keydown", function (e) {
    // Enter para submeter (se não estiver em um campo)
    if (e.key === "Enter" && e.target.tagName !== "INPUT") {
      e.preventDefault();
      const submitButton = document.getElementById("loginButton");
      if (submitButton && !submitButton.disabled) {
        submitButton.click();
      }
    }

    // Escape para limpar formulário
    if (e.key === "Escape") {
      clearForm();
    }
  });
}

// Limpar formulário
function clearForm() {
  const form = document.getElementById("loginForm");
  if (form) {
    form.reset();

    // Limpar estados de validação
    const inputs = form.querySelectorAll("input");
    inputs.forEach((input) => {
      clearValidationState(input);
    });

    // Focar no primeiro campo
    const firstInput = form.querySelector("input");
    if (firstInput) {
      firstInput.focus();
    }
  }
}

// Animações de entrada
function initAnimations() {
  // Animação escalonada dos elementos
  const elements = document.querySelectorAll(
    ".form-group, .btn-login, .back-link"
  );
  elements.forEach((element, index) => {
    element.style.opacity = "0";
    element.style.transform = "translateY(20px)";
    element.style.transition = "all 0.6s ease";

    setTimeout(() => {
      element.style.opacity = "1";
      element.style.transform = "translateY(0)";
    }, 200 + index * 100);
  });

  // Criar elementos decorativos
  createDecorations();
}

// Criar elementos decorativos
function createDecorations() {
  const loginPage = document.querySelector(".login-page");
  if (!loginPage) return;

  const decorationContainer = document.createElement("div");
  decorationContainer.className = "login-decoration";

  // Criar círculos decorativos
  for (let i = 0; i < 2; i++) {
    const circle = document.createElement("div");
    circle.className = "decoration-circle";
    decorationContainer.appendChild(circle);
  }

  loginPage.appendChild(decorationContainer);
}

// Toast notifications
function showToast(message, type = "info") {
  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
        <i class="fas fa-${getToastIcon(type)}"></i>
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

  Object.assign(toast.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    background: getToastColor(type),
    color: "white",
    padding: "16px 20px",
    borderRadius: "12px",
    display: "flex",
    alignItems: "center",
    gap: "12px",
    zIndex: "10000",
    boxShadow: "0 8px 25px rgba(0,0,0,0.15)",
    animation: "slideInRight 0.3s ease",
    fontWeight: "500",
    maxWidth: "400px",
  });

  document.body.appendChild(toast);

  // Auto remove após 4 segundos
  setTimeout(() => {
    toast.style.animation = "slideOutRight 0.3s ease";
    setTimeout(() => {
      if (document.body.contains(toast)) {
        document.body.removeChild(toast);
      }
    }, 300);
  }, 4000);
}

function getToastIcon(type) {
  const icons = {
    success: "check-circle",
    error: "exclamation-triangle",
    warning: "exclamation-circle",
    info: "info-circle",
  };
  return icons[type] || "info-circle";
}

function getToastColor(type) {
  const colors = {
    success: "#10b981",
    error: "#ef4444",
    warning: "#f59e0b",
    info: "#3b82f6",
  };
  return colors[type] || "#3b82f6";
}

// Adicionar estilos de animação
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
    
    .toast-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: background 0.2s ease;
    }
    
    .toast-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
`;

// Injetar estilos
if (!document.getElementById("login-toast-styles")) {
  const styleSheet = document.createElement("style");
  styleSheet.id = "login-toast-styles";
  styleSheet.textContent = toastStyles;
  document.head.appendChild(styleSheet);
}

// Função para focar no primeiro campo ao carregar
window.addEventListener("load", function () {
  const firstInput = document.querySelector("#loginForm input");
  if (firstInput) {
    setTimeout(() => {
      firstInput.focus();
    }, 500);
  }
});

// Detectar tentativas de login automatizadas
let loginAttempts = 0;
const maxAttempts = 5;

function trackLoginAttempt() {
  loginAttempts++;

  if (loginAttempts >= maxAttempts) {
    const submitButton = document.getElementById("loginButton");
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = "Muitas tentativas. Aguarde...";
    }

    showToast("Muitas tentativas de login. Aguarde alguns minutos.", "warning");

    // Reabilitar após 5 minutos
    setTimeout(() => {
      loginAttempts = 0;
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML =
          '<i class="fas fa-sign-in-alt"></i> Entrar no Sistema';
      }
    }, 300000); // 5 minutos
  }
}

// Verificar se há parâmetros de erro na URL
function checkURLParams() {
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get("error");

  if (error) {
    let message = "Erro no login";

    switch (error) {
      case "invalid":
        message = "Usuário ou senha inválidos";
        break;
      case "required":
        message = "Todos os campos são obrigatórios";
        break;
      case "blocked":
        message = "Conta temporariamente bloqueada";
        break;
    }

    showToast(message, "error");
    trackLoginAttempt();
  }
}

// Executar verificação ao carregar
checkURLParams();
