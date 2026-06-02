let feedbacks = Array.isArray(window.dbFeedbacks) ? window.dbFeedbacks : [];
const employees = Array.isArray(window.dbEmployees) ? window.dbEmployees : [];
const users = Array.isArray(window.dbUsers) ? window.dbUsers : [];

const openModalBtn = document.getElementById("openModalBtn");
const modal = document.getElementById("modal");
const closeModalBtn = document.getElementById("closeModalBtn");
const saveFeedbackBtn = document.getElementById("saveFeedback");
const modalTitle = document.getElementById("modalTitle");

const empInput = document.getElementById("fbEmployeeId");
const authorInput = document.getElementById("fbAuthorId");
const typeSelect = document.getElementById("fbType");
const contentInput = document.getElementById("fbContent");
const listContainer = document.getElementById("feedbackList");

let editingFeedback = null;
let editingAuthorName = "";

function resetForm() {
    empInput.value = "";
    authorInput.value = "";
    typeSelect.value = "";
    contentInput.value = "";
}

function openCreateModal() {
    editingFeedback = null;
    editingAuthorName = "";
    modalTitle.textContent = "Novo Feedback";
    saveFeedbackBtn.textContent = "Enviar";
    resetForm();
    modal.classList.remove("hidden");
}

function openEditModal(fb) {
    editingFeedback = fb;
    editingAuthorName = fb.author || "";
    modalTitle.textContent = "Editar Feedback";
    saveFeedbackBtn.textContent = "Salvar Alteracoes";
    empInput.value = fb.employeeId ? String(fb.employeeId) : "";
    authorInput.value = fb.authorId ? String(fb.authorId) : "";
    typeSelect.value = fb.type || "";
    contentInput.value = fb.content || "";
    modal.classList.remove("hidden");
}

openModalBtn.addEventListener("click", openCreateModal);

closeModalBtn.addEventListener("click", () => {
    modal.classList.add("hidden");
});

modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.classList.add("hidden");
});

saveFeedbackBtn.addEventListener("click", () => {
    if (!empInput.value.trim() || !contentInput.value.trim() || !typeSelect.value) {
        alert("Preencha todos os campos!");
        return;
    }

    if (!authorInput.value.trim() && !editingAuthorName) {
        alert("Selecione o autor.");
        return;
    }

    const employee = employees.find((e) => String(e.id) === String(empInput.value));
    const author = users.find((u) => String(u.id) === String(authorInput.value));
    if (!employee) {
        alert("Selecione funcionario e remetente validos.");
        return;
    }

    if (authorInput.value && !author) {
        alert("Selecione um autor valido.");
        return;
    }

    const authorName = author ? author.name : editingAuthorName;

    const formData = new FormData();
    formData.append("pFuncionarioId", employee.id);
    formData.append("pAutorId", author ? author.id : "");
    formData.append("pAutorNome", authorName);
    formData.append("pTipo", typeSelect.value);
    formData.append("pConteudo", contentInput.value.trim());
    formData.append("pDataFeedback", editingFeedback?.date || new Date().toISOString().slice(0, 10));

    if (editingFeedback) {
        formData.append("pId", editingFeedback.id);
    }

    const endpoint = editingFeedback ? "../scripts/acao_editar_feedback.php" : "../scripts/acao_incluir_feedback.php";

    fetch(endpoint, {
        method: "POST",
        body: formData
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.status === "success") {
                modal.classList.add("hidden");
                window.location.reload();
            } else {
                alert(data.message || "Erro ao salvar");
            }
        })
        .catch(() => alert("Erro na requisicao"));
});

function renderFeedbacks() {
    listContainer.innerHTML = "";

    if (feedbacks.length === 0) {
        listContainer.innerHTML = `
            <div class="empty">
                <p>Nenhum feedback encontrado.</p>
            </div>`;
        return;
    }

    feedbacks.forEach(fb => {
        const card = document.createElement("div");
        card.classList.add("feedback-card");

        const typeLabels = {
            positive: "Positivo",
            constructive: "Construtivo",
            neutral: "Neutro"
        };

        const typeIcons = {
            positive: "👍",
            constructive: "⚠️",
            neutral: "➖"
        };

        card.innerHTML = `
            <div class="fb-icon ${fb.type}">
                ${typeIcons[fb.type]}
            </div>

            <div class="fb-content">
                <div style="display:flex; gap:10px; align-items:center;">
                    <h3>Para: ${fb.employee}</h3>
                    <span class="fb-badge ${fb.type}">${typeLabels[fb.type]}</span>
                </div>

                <p>${fb.content}</p>

                <div class="fb-info">
                    <span>Por ${fb.author}</span>
                    <span>${fb.date}</span>
                </div>

                <div class="card-actions">
                    <button class="btn-secondary btn-small" data-action="edit" data-id="${fb.id}">Editar</button>
                    <button class="btn-danger btn-small" data-action="delete" data-id="${fb.id}">Excluir</button>
                </div>
            </div>
        `;

        listContainer.appendChild(card);
    });
}

renderFeedbacks();

listContainer.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;

    const id = Number(btn.dataset.id);
    const fb = feedbacks.find((item) => Number(item.id) === id);
    if (!fb) return;

    if (btn.dataset.action === "edit") {
        openEditModal(fb);
        return;
    }

    if (btn.dataset.action === "delete") {
        deleteFeedback(id);
    }
});

function deleteFeedback(id) {
    if (!confirm("Deseja excluir este feedback?")) {
        return;
    }

    const formData = new FormData();
    formData.append("pId", id);

    fetch("../scripts/acao_excluir_feedback.php", {
        method: "POST",
        body: formData
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.status === "success") {
                window.location.reload();
            } else {
                alert(data.message || "Erro ao excluir");
            }
        })
        .catch(() => alert("Erro na requisicao"));
}
