const main = document.getElementById("mainContent");
const navItems = document.querySelectorAll(".nav-item");

async function loadPage(page) {
    const response = await fetch(`../pages/${page}.html`);
    const html = await response.text();
    main.innerHTML = html;


    navItems.forEach(n => n.classList.remove("active"));


    document.querySelector(`.nav-item[data-page="${page}"]`).classList.add("active");


    loadPageScript(page);
}


function loadPageScript(page) {
    const scriptPath = `${page}.js`;

    fetch(scriptPath)
        .then(res => {
            if (res.ok) {
                const script = document.createElement("script");
                script.src = scriptPath;
                document.body.appendChild(script);
            }
        })
        .catch(() => {});
}

navItems.forEach(item => {
    item.addEventListener("click", () => {
        loadPage(item.dataset.page);
    });
});

loadPage("dashboard");
