const modal = document.getElementById("modal");
const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const saveBtn = document.getElementById("saveEvaluationBtn");

const list = document.getElementById("evaluationsList");

let evaluations = JSON.parse(localStorage.getItem("evaluations")) || [];

openModalBtn.onclick = () => (modal.style.display = "flex");
closeModalBtn.onclick = () => (modal.style.display = "none");

saveBtn.onclick = () => {
  const employeeName = document.getElementById("employeeName").value.trim();
  const type = document.getElementById("evaluationType").value;
  const score = document.getElementById("score").value;
  const evaluatorName = document.getElementById("evaluatorName").value.trim();
  const comments = document.getElementById("comments").value.trim();

  if (!employeeName || !type || !score || !evaluatorName) {
    alert("Preencha todos os campos obrigatórios!");
    return;
  }

  const evaluation = {
    id: Date.now(),
    employeeName,
    type,
    score: Number(score),
    evaluatorName,
    comments,
    date: new Date().toLocaleDateString("pt-BR"),
  };

  evaluations.unshift(evaluation);
  localStorage.setItem("evaluations", JSON.stringify(evaluations));

  render();
  modal.style.display = "none";
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
