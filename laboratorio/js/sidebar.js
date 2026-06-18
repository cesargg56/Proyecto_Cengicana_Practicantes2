const sidebar = document.getElementById("sidebar");
const toggleBtn = document.getElementById("toggleBtn");
const mainContent = document.getElementById("mainContent");

function toggleSidebar() {
    const isClosed = sidebar.classList.toggle("closed");

    mainContent.classList.toggle("active", !isClosed);
    toggleBtn.setAttribute("aria-expanded", String(!isClosed));
}

if (toggleBtn && sidebar && mainContent) {
    toggleBtn.setAttribute("role", "button");
    toggleBtn.setAttribute("aria-expanded", String(!sidebar.classList.contains("closed")));
    toggleBtn.setAttribute("tabindex", "0");

    toggleBtn.addEventListener("click", toggleSidebar);

    toggleBtn.addEventListener("touchend", (event) => {
        event.preventDefault();
        toggleSidebar();
    });

    toggleBtn.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            toggleSidebar();
        }
    });
}
