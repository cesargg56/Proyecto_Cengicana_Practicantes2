const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");

menuBtn.addEventListener("click", () => {

    sidebar.classList.toggle("sidebar-open");
    overlay.classList.toggle("hidden");

});

overlay.addEventListener("click", () => {

    sidebar.classList.remove("sidebar-open");
    overlay.classList.add("hidden");

});

window.addEventListener("resize", () => {

    if(window.innerWidth >= 1024){

        sidebar.classList.remove("sidebar-open");
        overlay.classList.add("hidden");

    }

});