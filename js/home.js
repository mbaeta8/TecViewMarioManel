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

document.addEventListener("DOMContentLoaded", () => {
    const profileImage = document.getElementById("profileImage");
    const profileImageDialog = document.getElementById("profileImageDialog");

    function handleImageChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImage.src = e.target.result;
                profileImageDialog.src = e.target.result;
                
                // Send image to PHP for saving
                uploadProfileImage(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    }

    function openFileExplorer() {
        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.accept = "image/*";
        fileInput.style.display = "none";

        fileInput.addEventListener("change", handleImageChange);
        document.body.appendChild(fileInput);
        fileInput.click();
        document.body.removeChild(fileInput);
    }
    
    profileImageDialog.addEventListener("click", openFileExplorer);

    function uploadProfileImage(base64Image) {
        fetch('./lib/upload_profileImage.php', {
            method: 'POST',
            body: JSON.stringify({ image: base64Image }),
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("Image uploaded successfully");
            } else {
                console.error("Error uploading image");
            }
        })
        .catch(error => console.error("Error:", error));
    }
});


let isEditing = false;

let originalValues = {};

function editMode() {
    console.log("ASDF");
    const inputs = document.querySelectorAll(".form input, .form textarea");
    const editButton = document.getElementById("editar");
    const saveButton = document.createElement("button");
    const cancelButton = document.createElement("button");

    // Almacena los valores originales antes de editar
    inputs.forEach(input => {
        originalValues[input.id] = input.value;
    });

    
    if (!isEditing) {
        inputs.forEach(input => {
            input.removeAttribute("readonly");
            input.style.backgroundColor = "#fff";
            input.style.border = "1px solid #ccc";
        });

        // Agregar botón de guardar si no existe
        let saveButton = document.getElementById("saveButton");
        if (!saveButton) {
            saveButton = document.createElement("button");
            saveButton.innerText = "Guardar";
            saveButton.id = "saveButton";
            saveButton.onclick = saveProfile;
            document.getElementById("profileButton").appendChild(saveButton);
        }

         // Crear y añadir botón de cancelar
        cancelButton.innerText = "Cancelar";
        cancelButton.id = "cancelButton";
        cancelButton.onclick = cancelEdit;
        document.getElementById("profileButton").appendChild(cancelButton);

        isEditing = true;
    }
    if (editButton) {
        editButton.style.display = "none"; // Ocultar el botón Editar
    }
}

function saveProfile() {
    const inputs = document.querySelectorAll(".form input, .form textarea");
    const editButton = document.getElementById("editar");
    const userData = {};

    inputs.forEach(input => {
        userData[input.placeholder] = input.value;
        input.setAttribute("readonly", true);
        input.style.backgroundColor = "transparent";
        input.style.border = "none";
    });

    // Eliminar el botón de guardar
    const saveButton = document.getElementById("saveButton");
    if (saveButton) {
        saveButton.remove();
    }
    if (editButton) {
        editButton.style.display = "block"; // Show the "Editar" button again
    }

    isEditing = false;

    // Enviar datos al servidor con AJAX
    fetch("./lib/update_profile.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(userData),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Perfil actualizado correctamente.");
        } else {
            alert("Hubo un error al actualizar.");
        }
    })
    .catch(error => console.error("Error:", error));
}

function cancelEdit() {
    const inputs = document.querySelectorAll(".form input, .form textarea");
    const editButton = document.getElementById("editar");

    // Restaura los valores originales de los inputs
    inputs.forEach(input => {
        input.value = originalValues[input.id] || ''; // Usamos el valor original almacenado
        input.setAttribute("readonly", true); // Vuelve a solo lectura
        input.style.backgroundColor = "transparent";
        input.style.border = "none";
        input.style.cursor = "default"; // Restaurar cursor
    });

    // Eliminar los botones de guardar y cancelar
    const saveButton = document.getElementById("saveButton");
    const cancelButton = document.getElementById("cancelButton");

    if (saveButton) {
        saveButton.remove();
    }
    if (cancelButton) {
        cancelButton.remove();
    }

    // Cambiar el modo a no editar
    isEditing = false;
    if (editButton) {
        editButton.style.display = "block"; // Show the "Editar" button again
    }
}
