
const employees = Array.isArray(window.dbEmployees) ? window.dbEmployees : [];


const employeeMap = employees.reduce((acc, e) => {
  acc[e.id] = e.name;
  return acc;
}, {});

function getEmployeeName(id) {
  if (!id) return "Desconhecido";
  return employeeMap[id] || id;
}

let goals = Array.isArray(window.dbGoals) ? window.dbGoals : [];

const modal = document.getElementById("modal");
const openBtn = document.getElementById("openModal");
const closeBtn = document.getElementById("closeModal");
const modalTitle = document.getElementById("modalTitle");
const saveBtn = document.getElementById("saveGoal");

let editingGoal = null;

function resetGoalForm() {
  document.getElementById("employeeSelect").value = "";
  document.getElementById("goalTitle").value = "";
  document.getElementById("goalDesc").value = "";
  document.getElementById("goalDate").value = "";
}

function openCreateModal() {
  editingGoal = null;
  modalTitle.textContent = "Criar Nova Meta";
  saveBtn.textContent = "Criar Meta";
  resetGoalForm();
  modal.classList.remove("hidden");
}

function openEditModal(goal) {
  editingGoal = goal;
  modalTitle.textContent = "Editar Meta";
  saveBtn.textContent = "Salvar Alteracoes";
  document.getElementById("employeeSelect").value = goal.employeeId ? String(goal.employeeId) : "";
  document.getElementById("goalTitle").value = goal.title || "";
  document.getElementById("goalDesc").value = goal.desc || "";
  document.getElementById("goalDate").value = goal.date || "";
  modal.classList.remove("hidden");
}

openBtn && (openBtn.onclick = openCreateModal);

closeBtn && (closeBtn.onclick = () => modal.classList.add("hidden"));
saveBtn && (saveBtn.onclick = () => {
  const employeeId = document.getElementById("employeeSelect").value;
  const title = document.getElementById("goalTitle").value.trim();
  const desc = document.getElementById("goalDesc").value.trim();
  const date = document.getElementById("goalDate").value;

  if (!employeeId || !title || !date) {
    alert("Preencha os campos obrigatórios (Funcionário, Título e Data).");
    return;
  }

  const formData = new FormData();
  formData.append("pFuncionarioId", employeeId);
  formData.append("pTitulo", title);
  formData.append("pDescricao", desc);
  formData.append("pDataLimite", date);
  formData.append("pStatus", editingGoal ? (editingGoal.status || "pendente") : "pendente");
  formData.append("pProgresso", editingGoal ? String(editingGoal.progress ?? 0) : "0");

  if (editingGoal) {
    formData.append("pId", editingGoal.id);
  }

  const endpoint = editingGoal ? "../scripts/acao_editar_meta.php" : "../scripts/acao_incluir_meta.php";

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


const container = document.getElementById("goalsList");
let currentFilter = "all";

function formatDateBR(isoDate) {
  if (!isoDate) return "";
  try {
    const d = new Date(isoDate);
    return d.toLocaleDateString("pt-BR");
  } catch {
    return isoDate;
  }
}

function renderGoals(filter = "all") {
  currentFilter = filter;
  if (!container) return;
  container.innerHTML = "";

  const filtered = goals.filter(g => filter === "all" || g.status === filter);

  if (filtered.length === 0) {
    container.innerHTML = `<div class="goal-card"><p>Nenhuma meta encontrada.</p></div>`;
    return;
  }

  filtered.forEach(goal => {
    const card = document.createElement("div");
    card.className = "goal-card";

    const employeeName = getEmployeeName(goal.employeeId);

    const statusLabel = goal.status === "pendente" ? "Pendente"
              : goal.status === "em_andamento" ? "Em andamento"
              : goal.status === "concluida" ? "Concluida"
              : goal.status === "cancelada" ? "Cancelada"
                      : goal.status;

    card.innerHTML = `
      <div class="goal-header">
        <h3>${escapeHtml(goal.title)}</h3>
        <p>${escapeHtml(goal.desc || "")}</p>
      </div>

      <div class="goal-info">
        <span><b>Funcionário:</b> ${escapeHtml(employeeName)}</span>
        <span><b>Prazo:</b> ${escapeHtml(formatDateBR(goal.date))}</span>
        <span><b>Status:</b> ${escapeHtml(statusLabel)}</span>
      </div>

      <div class="progress-container">
        <div class="progress-bar">
          <div class="progress-bar-fill" style="width:${goal.progress}%"></div>
        </div>
      </div>

      <div class="progress-buttons">
        <button data-id="${goal.id}" data-value="25">25%</button>
        <button data-id="${goal.id}" data-value="50">50%</button>
        <button data-id="${goal.id}" data-value="75">75%</button>
        <button data-id="${goal.id}" data-value="100">100%</button>
      </div>

      <div class="card-actions">
        <button class="btn-secondary btn-small" data-action="edit" data-id="${goal.id}">Editar</button>
        <button class="btn-danger btn-small" data-action="delete" data-id="${goal.id}">Excluir</button>
      </div>
    `;

    container.appendChild(card);
  });


  document.querySelectorAll(".progress-buttons button").forEach(btn => {
    btn.addEventListener("click", (e) => {
      const id = Number(btn.dataset.id);
      const value = Number(btn.dataset.value);
      updateProgress(id, value);
    });
  });
}


function updateProgress(id, value) {
  const goal = goals.find(g => g.id === id);
  if (!goal) return;

  const newStatus = value === 100 ? "concluida" : (value > 0 ? "em_andamento" : "pendente");

  const formData = new FormData();
  formData.append("pId", goal.id);
  formData.append("pFuncionarioId", goal.employeeId);
  formData.append("pTitulo", goal.title || "");
  formData.append("pDescricao", goal.desc || "");
  formData.append("pDataLimite", goal.date || "");
  formData.append("pStatus", newStatus);
  formData.append("pProgresso", String(value));

  fetch("../scripts/acao_editar_meta.php", {
    method: "POST",
    body: formData
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        goal.progress = value;
        goal.status = newStatus;
        renderGoals(currentFilter);
      } else {
        alert(data.message || "Erro ao atualizar");
      }
    })
    .catch(() => alert("Erro na requisicao"));
}


document.querySelectorAll(".filter-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    const filter = btn.dataset.filter || "all";
    renderGoals(filter);
  });
});


renderGoals();

container.addEventListener("click", (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const id = Number(btn.dataset.id);
  const goal = goals.find((item) => Number(item.id) === id);
  if (!goal) return;

  if (btn.dataset.action === "edit") {
    openEditModal(goal);
    return;
  }

  if (btn.dataset.action === "delete") {
    deleteGoal(id);
  }
});

function deleteGoal(id) {
  if (!confirm("Deseja excluir esta meta?")) {
    return;
  }

  const formData = new FormData();
  formData.append("pId", id);

  fetch("../scripts/acao_excluir_meta.php", {
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


function escapeHtml(str) {
  if (typeof str !== "string") return str;
  return str.replace(/[&<>"']/g, function (m) {
    return ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    })[m];
  });
}
