document.addEventListener("DOMContentLoaded", function () {
    let panel = document.getElementById("sidePanel");
    let logo = document.getElementById("togglePanel");
    let darkModeButton = document.getElementById("darkModeToggle"); // ?? CORREGIDO

    // Alternar panel lateral
    if (logo && panel) {
        logo.addEventListener("click", function () {
            panel.classList.toggle("active");
        });
    }

    // Alternar modo oscuro
    if (darkModeButton) {
        darkModeButton.addEventListener("change", function () {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("darkMode", document.body.classList.contains("dark-mode"));
        });

        // Mantener el modo oscuro activado si estaba antes
        if (localStorage.getItem("darkMode") === "true") {
            document.body.classList.add("dark-mode");
            darkModeButton.checked = true;
        }
    }
});
