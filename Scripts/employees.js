let employees = Array.isArray(window.dbEmployees) ? window.dbEmployees : [];

const grid = document.getElementById("employeesGrid");
const searchInput = document.getElementById("searchInput");
const emptyState = document.getElementById("emptyState");
const modalTitle = document.getElementById("modalTitle");
const empIdInput = document.getElementById("empId");

let editingEmployee = null;

function renderEmployees() {
    grid.innerHTML = "";
    const term = searchInput.value.toLowerCase();

    const filtered = employees.filter(emp =>
        emp.name.toLowerCase().includes(term) ||
        emp.position.toLowerCase().includes(term) ||
        emp.department.toLowerCase().includes(term)
    );

    if (filtered.length === 0) {
        emptyState.classList.remove("hidden");
        return;
    } else {
        emptyState.classList.add("hidden");
    }

    filtered.forEach(emp => {
        const initials = emp.name.split(" ").map(n => n[0]).join("").slice(0, 2);

        let scoreClass =
            emp.performanceScore >= 90 ? "green" :
            emp.performanceScore >= 75 ? "blue" :
            emp.performanceScore >= 60 ? "yellow" : "red";

        grid.innerHTML += `
            <div class="card">
                <div class="card-header">
                    <div class="initials">${initials}</div>
                    <div class="score ${scoreClass}">${emp.performanceScore}</div>
                </div>

                <h3>${emp.name}</h3>
                <p>${emp.position}</p>

                <div class="info">
                    <div class="info-row">📧 ${emp.email}</div>
                    <div class="info-row">🏢 ${emp.department}</div>
                    <div class="info-row">📅 Desde ${new Date(emp.hireDate).toLocaleDateString("pt-BR")}</div>
                </div>

                <div class="progress-area">
                    <p>Performance</p>
                    <div class="progress">
                        <div class="progress-fill" style="width: ${emp.performanceScore}%"></div>
                    </div>
                </div>

                <div class="card-actions">
                    <button class="btn-secondary btn-small" data-action="report" data-id="${emp.id}">Gerar PDF</button>
                    <button class="btn-secondary btn-small" data-action="edit" data-id="${emp.id}">Editar</button>
                    <button class="btn-danger btn-small" data-action="delete" data-id="${emp.id}">Excluir</button>
                </div>
            </div>
        `;
    });
}

renderEmployees();
searchInput.addEventListener("input", renderEmployees);

const modal = document.getElementById("modal");
const openModal = document.getElementById("openModal");
const closeModal = document.getElementById("closeModal");
const saveEmployee = document.getElementById("saveEmployee");

function resetEmployeeForm() {
    empIdInput.value = "";
    document.getElementById("empName").value = "";
    document.getElementById("empEmail").value = "";
    document.getElementById("empCpf").value = "";
    document.getElementById("empBirth").value = "";
    document.getElementById("empPhone").value = "";
    document.getElementById("empPosition").value = "";
    document.getElementById("empDepartment").value = "";
    document.getElementById("empHireDate").value = "";
    document.getElementById("empPerformance").value = "0";
}

function openCreateModal() {
    editingEmployee = null;
    modalTitle.textContent = "Adicionar Funcionario";
    saveEmployee.textContent = "Adicionar";
    resetEmployeeForm();
    modal.classList.remove("hidden");
}

function openEditModal(emp) {
    editingEmployee = emp;
    empIdInput.value = emp.id;
    modalTitle.textContent = "Editar Funcionario";
    saveEmployee.textContent = "Salvar Alteracoes";
    document.getElementById("empName").value = emp.name || "";
    document.getElementById("empEmail").value = emp.email || "";
    document.getElementById("empCpf").value = emp.cpf || "";
    document.getElementById("empBirth").value = emp.birth || "";
    document.getElementById("empPhone").value = emp.phone || "";
    document.getElementById("empPosition").value = emp.position || "";
    document.getElementById("empDepartment").value = emp.department || "";
    document.getElementById("empHireDate").value = emp.hireDate || "";
    document.getElementById("empPerformance").value = typeof emp.performanceScore === "number" ? String(emp.performanceScore) : "0";
    modal.classList.remove("hidden");
}

openModal.onclick = openCreateModal;
closeModal.onclick = () => modal.classList.add("hidden");

grid.addEventListener("click", (e) => {
    const btn = e.target.closest("button[data-action]");
    if (!btn) return;
    const id = Number(btn.dataset.id);
    const emp = employees.find((item) => Number(item.id) === id);
    if (!emp) return;

    if (btn.dataset.action === "edit") {
        openEditModal(emp);
        return;
    }

    if (btn.dataset.action === "delete") {
        deleteEmployee(id);
        return;
    }

    if (btn.dataset.action === "report") {
        openEmployeeReport(id);
    }
});


function openEmployeeReport(id) {
    window.open(`../scripts/relatorio_pdf.php?id=${id}`, "_blank");
}

function deleteEmployee(id) {
    if (!confirm("Deseja excluir este funcionario?")) {
        return;
    }

    const formData = new FormData();
    formData.append("pId", id);

    fetch("../scripts/acao_excluir_pessoa.php", {
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

saveEmployee.onclick = () => {
    const name = document.getElementById("empName").value;
    const email = document.getElementById("empEmail").value;
    const cpf = document.getElementById("empCpf").value;
    const birth = document.getElementById("empBirth").value;
    const phone = document.getElementById("empPhone").value;
    const position = document.getElementById("empPosition").value;
    const department = document.getElementById("empDepartment").value;
    const hireDate = document.getElementById("empHireDate").value;
    const performance = document.getElementById("empPerformance").value;

    if (!name || !email || !cpf || !birth || !phone || !position || !department || !hireDate) {
        alert("Preencha todos os campos!");
        return;
    }

    const formData = new FormData();
    const personTypeId = editingEmployee && editingEmployee.personTypeId ? editingEmployee.personTypeId : "1";
    formData.append("pNome", name);
    formData.append("pCpf", cpf);
    formData.append("pNascimento", birth);
    formData.append("pTelefone", phone);
    formData.append("pPessoaTipoId", personTypeId);
    formData.append("pEmail", email);
    formData.append("pCargo", position);
    formData.append("pDepartamento", department);
    formData.append("pDataAdmissao", hireDate);
    formData.append("pPerformanceScore", performance || "0");

    if (editingEmployee) {
        formData.append("pId", editingEmployee.id);
    }

    const endpoint = editingEmployee ? "../scripts/acao_editar_pessoa.php" : "../scripts/acao_incluir_pessoa.php";

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