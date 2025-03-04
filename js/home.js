document.addEventListener("DOMContentLoaded", function () {
    let panel = document.getElementById("sidePanel");
    let logo = document.getElementById("togglePanel");
    let darkModeToggle = document.getElementById("darkModeToggle");
    let footer = document.querySelector("footer");

    // ? Alternar el panel lateral al hacer clic en el logo
    logo.addEventListener("click", function () {
        panel.classList.toggle("active");
    });

    // ? Restaurar estado del modo oscuro desde localStorage
    if (localStorage.getItem("dark-mode") === "enabled") {
        document.body.classList.add("dark-mode");
        panel.classList.add("dark-mode");
        footer.classList.add("dark-mode");
        darkModeToggle.checked = true;
    }

    // ? Cambiar modo oscuro y guardar en localStorage
    darkModeToggle.addEventListener("change", function () {
        if (darkModeToggle.checked) {
            document.body.classList.add("dark-mode");
            panel.classList.add("dark-mode");
            footer.classList.add("dark-mode");
            localStorage.setItem("dark-mode", "enabled");
        } else {
            document.body.classList.remove("dark-mode");
            panel.classList.remove("dark-mode");
            footer.classList.remove("dark-mode");
            localStorage.setItem("dark-mode", "disabled");
        }
    });
});
