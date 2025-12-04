// Carregar dados
let feedbacks = JSON.parse(localStorage.getItem("feedbacks")) || [];

// Elementos
const openModalBtn = document.getElementById("openModalBtn");
const modal = document.getElementById("modal");
const closeModalBtn = document.getElementById("closeModalBtn");
const saveFeedbackBtn = document.getElementById("saveFeedback");

const empInput = document.getElementById("fbEmployee");
const authorInput = document.getElementById("fbAuthor");
const typeSelect = document.getElementById("fbType");
const contentInput = document.getElementById("fbContent");
const listContainer = document.getElementById("feedbackList");


openModalBtn.addEventListener("click", () => {
    modal.classList.remove("hidden");
});

closeModalBtn.addEventListener("click", () => {
    modal.classList.add("hidden");
});


modal.addEventListener("click", (e) => {
    if (e.target === modal) modal.classList.add("hidden");
});


saveFeedbackBtn.addEventListener("click", () => {
    if (!empInput.value.trim() || !authorInput.value.trim() || !contentInput.value.trim() || !typeSelect.value) {
        alert("Preencha todos os campos!");
        return;
    }

    const fb = {
        id: Date.now(),
        employee: empInput.value,
        author: authorInput.value,
        type: typeSelect.value,
        content: contentInput.value,
        date: new Date().toLocaleDateString("pt-BR")
    };

    feedbacks.unshift(fb);
    localStorage.setItem("feedbacks", JSON.stringify(feedbacks));

    renderFeedbacks();
    modal.classList.add("hidden");

    empInput.value = "";
    authorInput.value = "";
    typeSelect.value = "";
    contentInput.value = "";
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
            </div>
        `;

        listContainer.appendChild(card);
    });
}

renderFeedbacks();
