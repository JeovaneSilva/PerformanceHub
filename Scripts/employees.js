let employees = [
    {
        id: "1",
        name: "Ana Silva",
        email: "ana.silva@empresa.com",
        position: "Desenvolvedora Senior",
        department: "Tecnologia",
        hireDate: "2022-03-14",
        performanceScore: 92
    },
    {
        id: "2",
        name: "Carlos Santos",
        email: "carlos.santos@empresa.com",
        position: "Gerente de Projetos",
        department: "Operações",
        hireDate: "2021-08-19",
        performanceScore: 88
    }
];

const grid = document.getElementById("employeesGrid");
const searchInput = document.getElementById("searchInput");
const emptyState = document.getElementById("emptyState");

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
        const initials = emp.name.split(" ").map(n => n[0]).join("").slice(0,2);

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

openModal.onclick = () => modal.classList.remove("hidden");
closeModal.onclick = () => modal.classList.add("hidden");

saveEmployee.onclick = () => {
    const name = document.getElementById("empName").value;
    const email = document.getElementById("empEmail").value;
    const position = document.getElementById("empPosition").value;
    const department = document.getElementById("empDepartment").value;
    const hireDate = document.getElementById("empHireDate").value;

    if (!name || !email || !position || !department || !hireDate) {
        alert("Preencha todos os campos!");
        return;
    }

    employees.push({
        id: Date.now(),
        name,
        email,
        position,
        department,
        hireDate,
        performanceScore: Math.floor(Math.random() * 40) + 60
    });

    modal.classList.add("hidden");
    renderEmployees();
};
