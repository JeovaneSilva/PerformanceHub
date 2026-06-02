const modal = document.getElementById("modal");
const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const saveBtn = document.getElementById("saveEvaluationBtn");
const modalTitle = document.getElementById("modalTitle");

const list = document.getElementById("evaluationsList");

const employees = Array.isArray(window.dbEmployees) ? window.dbEmployees : [];
const users = Array.isArray(window.dbUsers) ? window.dbUsers : [];
let evaluations = Array.isArray(window.dbEvaluations) ? window.dbEvaluations : [];

let editingEvaluation = null;

function resetForm() {
  document.getElementById("employeeId").value = "";
  document.getElementById("evaluationType").value = "mensal";
  document.getElementById("score").value = "";
  document.getElementById("evaluatorId").value = "";
  document.getElementById("comments").value = "";
}

function openCreateModal() {
  editingEvaluation = null;
  modalTitle.textContent = "Registrar Avaliacao";
  saveBtn.textContent = "Salvar";
  resetForm();
  modal.classList.remove("hidden");
}

function openEditModal(ev) {
  editingEvaluation = ev;
  modalTitle.textContent = "Editar Avaliacao";
  saveBtn.textContent = "Salvar Alteracoes";
  document.getElementById("employeeId").value = ev.employeeId ? String(ev.employeeId) : "";
  document.getElementById("evaluationType").value = ev.type || "mensal";
  document.getElementById("score").value = ev.score ?? "";

  const evaluator = users.find((u) => u.name === ev.evaluatorName);
  document.getElementById("evaluatorId").value = evaluator ? String(evaluator.id) : "";

  document.getElementById("comments").value = ev.comments || "";
  modal.classList.remove("hidden");
}

openModalBtn.onclick = openCreateModal;
closeModalBtn.onclick = () => modal.classList.add("hidden");

saveBtn.onclick = () => {
  const employeeId = document.getElementById("employeeId").value;
  const type = document.getElementById("evaluationType").value;
  const score = document.getElementById("score").value;
  const evaluatorId = document.getElementById("evaluatorId").value;
  const comments = document.getElementById("comments").value.trim();

  if (!employeeId || !type || !score || !evaluatorId) {
    alert("Preencha todos os campos obrigatorios!");
    return;
  }

  const employee = employees.find((e) => String(e.id) === String(employeeId));
  const evaluator = users.find((u) => String(u.id) === String(evaluatorId));
  if (!employee || !evaluator) {
    alert("Selecione funcionario e avaliador validos.");
    return;
  }

  const formData = new FormData();
  formData.append("pFuncionarioId", employee.id);
  formData.append("pAvaliacaoStatusId", editingEvaluation?.statusId ?? "1");
  formData.append("pData", editingEvaluation?.date || new Date().toISOString().slice(0, 10));
  formData.append("pTipo", type);
  formData.append("pPontuacao", score);
  formData.append("pAvaliadorNome", evaluator.name);
  formData.append("pObservacao", comments);

  if (editingEvaluation) {
    formData.append("pId", editingEvaluation.id);
  }

  const endpoint = editingEvaluation ? "../scripts/acao_editar_avaliacao.php" : "../scripts/acao_incluir_avaliacao.php";

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
};

function render() {
  list.innerHTML = "";

  if (evaluations.length === 0) {
    list.innerHTML = `<p>Nenhuma avaliação registrada.</p>`;
    return;
  }

  evaluations.forEach((ev) => {
    const div = document.createElement("div");
    div.className = "card";

    div.innerHTML = `
    <div class="card-left">
        <h3>${ev.employeeName}</h3>

        <div class="card-info">
            <span class="tag ${ev.type}">${ev.type}</span>
            <span>• Avaliado por <strong>${ev.evaluatorName}</strong></span>
            <span>• ${ev.date}</span>
        </div>

        <p>${ev.comments}</p>

      <div class="card-actions">
        <button class="btn-secondary btn-small" data-action="edit" data-id="${ev.id}">Editar</button>
        <button class="btn-danger btn-small" data-action="delete" data-id="${ev.id}">Excluir</button>
      </div>
    </div>

    <div class="card-score">
        ${ev.score}
        <span>pontos</span>
    </div>
`;

    list.appendChild(div);
  });
}

render();

list.addEventListener("click", (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const id = Number(btn.dataset.id);
  const ev = evaluations.find((item) => Number(item.id) === id);
  if (!ev) return;

  if (btn.dataset.action === "edit") {
    openEditModal(ev);
    return;
  }

  if (btn.dataset.action === "delete") {
    deleteEvaluation(id);
  }
});

function deleteEvaluation(id) {
  if (!confirm("Deseja excluir esta avaliacao?")) {
    return;
  }

  const formData = new FormData();
  formData.append("pId", id);

  fetch("../scripts/acao_excluir_avaliacao.php", {
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
