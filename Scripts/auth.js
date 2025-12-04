
(() => {
  const form = document.getElementById('loginForm');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const submitBtn = document.getElementById('submitBtn');
  const btnText = document.getElementById('btnText');
  const toastRoot = document.getElementById('toast');

  if (!form || !emailInput || !passwordInput || !submitBtn || !btnText || !toastRoot) {
    console.warn('auth.js: elementos não encontrados. Verifique IDs no HTML.');
    return;
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

  function loginSimulator(email, password) {
    return new Promise((resolve) => {
      setTimeout(() => {
        if (email && password.length >= 6) {
          const user = {
            id: '1',
            name: email.split('@')[0],
            email,
          };
          localStorage.setItem('user', JSON.stringify(user));
          resolve(true);
        } else {
          resolve(false);
        }
      }, 900);
    });
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

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const email = (emailInput.value || '').trim();
    const password = (passwordInput.value || '').trim();

    if (!validate(email, password)) return;

    try {
      setLoading(true);
      const success = await loginSimulator(email, password);
      if (success) {
        showToast('Login realizado com sucesso!', 'success');
        setTimeout(() => {
          window.location.href = 'dashboard.html';
        }, 700);
      } else {
        showToast('Credenciais inválidas', 'error');
      }
    } catch (err) {
      console.error(err);
      showToast('Erro ao tentar logar', 'error');
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
