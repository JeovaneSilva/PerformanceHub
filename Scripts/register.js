
function setupPasswordToggle(toggleId, inputId) {
    const toggleIcon = document.getElementById(toggleId);
    const passInput = document.getElementById(inputId);

    if (toggleIcon && passInput) {
        toggleIcon.addEventListener('click', function () {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            
            if (type === 'text') {
                this.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>';
            } else {
                this.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>';
            }
        });
    }
}

setupPasswordToggle('toggleRegPassword', 'password');
setupPasswordToggle('toggleRegConfirm', 'confirmPassword');


function showToast(msg, isError = false) {
    const toast = document.getElementById("toast");
    toast.textContent = msg;
    toast.style.backgroundColor = isError ? "#c0392b" : "#138a3a"; 
    toast.classList.add("toast-show");

    setTimeout(() => {
        toast.classList.remove("toast-show");
    }, 3000);
}

document.getElementById("registerForm").addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();
    const submitBtn = document.getElementById("submitBtn");

    if (!name || !email || !password || !confirmPassword) {
        return showToast("Preencha todos os campos!", true);
    }

    if (password.length < 6) {
        return showToast("A senha deve ter pelo menos 6 caracteres!", true);
    }

    if (password !== confirmPassword) {
        return showToast("As senhas não coincidem!", true);
    }

    submitBtn.textContent = "Criando...";
    submitBtn.setAttribute('disabled', 'true');

    $.ajax({
        type: 'POST',
        url: '../scripts/usuario_inserir.php',
        data: {
            pNome: name,
            pEmail: email,
            pSenha: password
        },
        success: function(data) {
            let vRetorno = data.replace(/[\[\]]/g, '').trim();
            
            if (vRetorno === "1") {
                showToast("Conta criada com sucesso!");
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 1500);
            } else if (vRetorno === "2") {
                showToast("Este e-mail já está cadastrado!", true);
                submitBtn.textContent = "Criar conta";
                submitBtn.removeAttribute('disabled');
            } else {
                showToast("Erro ao criar conta. Tente novamente.", true);
                submitBtn.textContent = "Criar conta";
                submitBtn.removeAttribute('disabled');
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
            showToast("Erro no servidor.", true);
            submitBtn.textContent = "Criar conta";
            submitBtn.removeAttribute('disabled');
        }
    });
});