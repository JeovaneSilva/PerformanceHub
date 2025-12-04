
const employees = [
  { id: "1", name: "João Santos" },
  { id: "2", name: "Maria Oliveira" },
  { id: "3", name: "Carlos Lima" }
];


const employeeMap = employees.reduce((acc, e) => {
  acc[e.id] = e.name;
  return acc;
}, {});

function getEmployeeName(id) {
  if (!id) return "Desconhecido";
  return employeeMap[id] || id;
}

let goals = [];

const modal = document.getElementById("modal");
const openBtn = document.getElementById("openModal");
const closeBtn = document.getElementById("closeModal");

openBtn && (openBtn.onclick = () => {
 
  modal.style.display = "flex";
  document.getElementById("employeeSelect").value = "";
  document.getElementById("goalTitle").value = "";
  document.getElementById("goalDesc").value = "";
  document.getElementById("goalDate").value = "";
});

closeBtn && (closeBtn.onclick = () => modal.style.display = "none");


const saveBtn = document.getElementById("saveGoal");
saveBtn && (saveBtn.onclick = () => {
  const employeeId = document.getElementById("employeeSelect").value;
  const title = document.getElementById("goalTitle").value.trim();
  const desc = document.getElementById("goalDesc").value.trim();
  const date = document.getElementById("goalDate").value;

  if (!employeeId || !title || !date) {
    alert("Preencha os campos obrigatórios (Funcionário, Título e Data).");
    return;
  }

  goals.push({
    id: Date.now(),
    employeeId,     
    title,
    desc,
    date,
    progress: 0,
    status: "pending"
  });

  modal.style.display = "none";
  renderGoals(currentFilter); 
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

    const statusLabel = goal.status === "pending" ? "Pendente"
                      : goal.status === "in_progress" ? "Em andamento"
                      : goal.status === "completed" ? "Concluída"
                      : goal.status === "cancelled" ? "Cancelada"
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
  goals = goals.map(g => {
    if (g.id === id) {
      g.progress = value;
      g.status = value === 100 ? "completed" : (value > 0 ? "in_progress" : "pending");
    }
    return g;
  });
  renderGoals(currentFilter);
}


document.querySelectorAll(".filter-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    const filter = btn.dataset.filter || "all";
    renderGoals(filter);
  });
});


goals = [
  {
    id: 1,
    employeeId: "1",
    title: "Implementar sistema de autenticação",
    desc: "OAuth2 + refresh tokens",
    date: "2024-02-28",
    progress: 75,
    status: "in_progress"
  },
  {
    id: 2,
    employeeId: "2",
    title: "Redesign do aplicativo mobile",
    desc: "Acessibilidade e performance",
    date: "2024-04-15",
    progress: 40,
    status: "pending"
  },
  {
    id: 3,
    employeeId: "3",
    title: "Campanha de lançamento Q1",
    desc: "Planejar e executar campanha",
    date: "2024-02-15",
    progress: 100,
    status: "completed"
  }
];

renderGoals();


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
