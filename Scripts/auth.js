(() => {
  const form = document.getElementById('loginForm');
  const emailInput = document.getElementById('login') || document.getElementById('email'); 
  const passwordInput = document.getElementById('senha') || document.getElementById('password');
  const submitBtn = document.getElementById('submitBtn');
  const btnText = document.getElementById('btnText');
  const toastRoot = document.getElementById('toast');

  if (!form || !emailInput || !passwordInput || !submitBtn || !btnText || !toastRoot) {
    console.warn('auth.js: elementos não encontrados. Verifique IDs no HTML.');
    return;
  }

  const togglePassword = document.getElementById('togglePassword');
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      if (type === 'text') {
        this.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>';
      } else {
        this.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>';
      }
    });
  }

  function showToast(message, type = 'info', duration = 3000) {
    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.textContent = message;
    toastRoot.appendChild(t);

    requestAnimationFrame(() => t.classList.add('toast--visible'));

    setTimeout(() => {
      t.classList.remove('toast--visible');
      setTimeout(() => toastRoot.removeChild(t), 300);
    }, duration);
  }

  function setLoading(isLoading) {
    if (isLoading) {
      submitBtn.setAttribute('disabled', 'disabled');
      btnText.textContent = 'Entrando...';
      submitBtn.classList.add('btn--loading');
    } else {
      submitBtn.removeAttribute('disabled');
      btnText.textContent = 'Entrar';
      submitBtn.classList.remove('btn--loading');
    }
  }

  function validate(email, password) {
    if (!email || !password) {
      showToast('Preencha todos os campos', 'error');
      return false;
    }
    if (password.length < 6) {
      showToast('A senha deve ter pelo menos 6 caracteres', 'error');
      return false;
    }
    return true;
  }


  function realizarLoginPHP(usuario, senha) {
    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'POST',
        url: '../scripts/login_validar.php',
        data: {
          pLogin: usuario, 
          pSenha: senha 
        },
        success: function(data) {
          let vRetorno = data.replace(/[\[\]]/g, '').trim();
          
          if (vRetorno === "1") {
            resolve(true); 
          } else {
            resolve(false);
          }
        },
        error: function(xhr, status, error) {
          console.error("Erro no AJAX: ", error);
          reject(error);
        }
      });
    });
  }

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const email = (emailInput.value || '').trim();
    const password = (passwordInput.value || '').trim();

    if (!validate(email, password)) return;

    try {
      setLoading(true);
      const success = await realizarLoginPHP(email, password);
      
      if (success) {
        showToast('Login realizado com sucesso!', 'success');
        
        setTimeout(() => {
          window.location.href = '../pages/dashboard.php';
        }, 700);
      } else {
        showToast('Credenciais inválidas. Tente novamente.', 'error');
        passwordInput.value = '';
      }
    } catch (err) {
      showToast('Erro de comunicação com o servidor', 'error');
    } finally {
      setLoading(false);
    }
  });

  const style = document.createElement('style');
  style.textContent = `
    /* Toasts */
    .toast-container { position: fixed; right: 20px; top: 20px; z-index: 9999; display:flex; flex-direction:column; gap:10px; }
    .toast { min-width: 180px; padding: 10px 14px; border-radius: 8px; color: #fff; transform: translateY(-6px) scale(.98); opacity: 0; transition: all .18s ease; box-shadow: 0 6px 18px rgba(0,0,0,.06); font-weight:600; }
    .toast--visible { transform: translateY(0) scale(1); opacity: 1; }
    .toast-info { background: #333; }
    .toast-success { background: #138a3a; }
    .toast-error { background: #c0392b; }

    /* botão loading (simples) */
    .btn--loading { opacity: 0.9; pointer-events: none; }
  `;
  document.head.appendChild(style);
})();