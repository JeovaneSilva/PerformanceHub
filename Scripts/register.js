function showToast(msg) {
    const toast = document.getElementById("toast");
    toast.textContent = msg;
    toast.classList.add("toast-show");

    setTimeout(() => {
        toast.classList.remove("toast-show");
    }, 3000);
}

function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
}

document.getElementById("registerForm").addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();

    if (!name || !email || !password || !confirmPassword) {
        return showToast("Preencha todos os campos!");
    }

    if (password.length < 6) {
        return showToast("A senha deve ter pelo menos 6 caracteres!");
    }

    if (password !== confirmPassword) {
        return showToast("As senhas não coincidem!");
    }

    const userData = { name, email, password };
    setCookie("user", JSON.stringify(userData), 7);

    showToast("Conta criada com sucesso!");

    setTimeout(() => {
        window.location.href = "../pages/index.html";
    }, 1200);
});
