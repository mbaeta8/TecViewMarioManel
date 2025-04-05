document.addEventListener("DOMContentLoaded", function () {
    let panel = document.getElementById("sidePanel");
    let logo = document.getElementById("togglePanel");
    let darkModeButton = document.getElementById("darkModeToggle");
    let sunIcon = document.getElementById("sunIcon");
    let moonIcon = document.getElementById("moonIcon");

    // Alternar panel lateral
    if (logo && panel) {
        logo.addEventListener("click", function () {
            panel.classList.toggle("active");
        });
    }

    // Modo oscuro
    if (darkModeButton) {
        darkModeButton.addEventListener("change", function () {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("darkMode", document.body.classList.contains("dark-mode"));
            updateThemeIcons();
        });

        if (localStorage.getItem("darkMode") === "true") {
            document.body.classList.add("dark-mode");
            darkModeButton.checked = true;
            updateThemeIcons();
        }
    }

    function updateThemeIcons() {
        if (darkModeButton.checked) {
            sunIcon.style.display = "none";
            moonIcon.style.display = "inline";
        } else {
            sunIcon.style.display = "inline";
            moonIcon.style.display = "none";
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

document.addEventListener("DOMContentLoaded", function () {
    // Variables
    const createPostForm = document.getElementById("createPostForm");
    const mediaTypeSelect = document.getElementById("mediaTypeSelect");
    const feed = document.getElementById("feed");

    // Crear publicación
    createPostForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const description = document.getElementById("description").value.trim();
        const mediaType = mediaTypeSelect.value;
        const image = document.getElementById("image").files[0];
        const video = document.getElementById("video").files[0];
        const gifUrl = document.getElementById("gif").value.trim();

        // Validación de campos
        if (description === "") {
            alert("Por favor ingrese una descripción.");
            return;
        }
        if (mediaType === "none") {
            alert("Por favor seleccione un tipo de medio.");
            return;
        }

        // Validación de tamaño de video
        const maxSize = 10 * 1024 * 1024; // Tamaño máximo 10MB
        if (video && video.size > maxSize) {
            alert("El video es demasiado grande. El tamaño máximo permitido es 10MB.");
            return;
        }

        if (mediaType === "image" && !image) {
            alert("Por favor cargue una imagen.");
            return;
        }
        if (mediaType === "video" && !video) {
            alert("Por favor cargue un video.");
            return;
        }
        if (mediaType === "gif" && !gifUrl) {
            alert("Por favor ingrese una URL de GIF.");
            return;
        }

        const formData = new FormData();
        formData.append("description", description);
        formData.append("mediaType", mediaType);

        if (mediaType === "image" && image) {
            formData.append("image", image);
        }

        if (mediaType === "video" && video) {
            if (video.type.startsWith("video")) {
                formData.append("video", video);  // Enviar el video directamente si es un archivo de video
            } else {
                // Si no es video, lo intentamos en base64
                const reader = new FileReader();
                reader.onloadend = function () {
                    const base64Video = reader.result.split(',')[1];  // Quitar el prefijo base64
                    formData.append("video", base64Video);  // Añadir el video como base64
                    sendFormData(formData);  // Enviar el FormData
                };
                reader.readAsDataURL(video);  // Convierte el video a base64
                return;  // Aseguramos que no se envíe hasta que el FileReader haya terminado
            }
        }

        if (mediaType === "gif" && gifUrl) {
            formData.append("gifUrl", gifUrl);
        }

        sendFormData(formData);  // Enviar el FormData

        // Función para enviar el FormData al servidor
        function sendFormData(formData) {
            fetch('./lib/create_post.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin' // Incluir credenciales para la sesión
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta del servidor');
                return response.json();
            })
            .then(data => {
                // Verifica que la respuesta sea un objeto y contenga la clave "success"
                if (!data || typeof data !== 'object') {
                    throw new Error('La respuesta del servidor no es válida');
                }

                if (data.success) {
                    alert("Publicación creada exitosamente");
                    if (data.post) {
                        addPostToFeed(data.post); // Solo si el servidor devuelve el post
                    }
                    createPostForm.reset(); // Limpia el formulario
                } else {
                    alert("Hubo un error al crear la publicación: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                alert("Hubo un error al intentar crear la publicación.");
            });
        }
    });

    // Función para agregar la publicación al feed
    function addPostToFeed(post) {
        const feed = document.getElementById("feed");
        if (!feed) return;

        const postElement = document.createElement("div");
        postElement.classList.add("post");

        let mediaContent = "";
        if (post.mediaType === "image" && post.image) {
            mediaContent = `<img src="${post.image}" alt="Publicación">`;
        } else if (post.mediaType === "video" && post.video) {
            mediaContent = `<video controls><source src="${post.video}" type="video/mp4"></video>`;
        } else if (post.mediaType === "gif" && post.gifUrl) {
            mediaContent = `<img src="${post.gifUrl}" alt="GIF" class="gif">`;
        }

        postElement.innerHTML = `
            <div class="post-header">
                <img src="${post.profileImage}" alt="Foto de perfil" class="profile-pic">
                <strong>${post.username}</strong>
                <span>${post.date}</span>
            </div>
            <p>${post.description}</p>
            ${mediaContent}
            <div class="post-buttons">
                <button class="like-btn" data-id="${post.postID}">👍 <span>${post.likes}</span></button>
                <button class="dislike-btn" data-id="${post.postID}">👎 <span>${post.dislikes}</span></button>
                <button class="comment-btn" data-id="${post.postID}">💬</button>
            </div>
            <div class="comments-section" id="comments-${post.postID}"></div>
        `;

        feed.prepend(postElement);

        setTimeout(() => {
            postElement.scrollIntoView({ behavior: "smooth", block: "center" });
        }, 300);
    }

    // Toggle de campos según el tipo de medio seleccionado
    mediaTypeSelect.addEventListener("change", toggleMediaFields);
    toggleMediaFields(); // Inicializa el estado de los campos

    function toggleMediaFields() {
        const mediaType = mediaTypeSelect.value;
        document.getElementById("imageField").style.display = "none";
        document.getElementById("videoField").style.display = "none";
        document.getElementById("gifField").style.display = "none";

        if (mediaType === "image") {
            document.getElementById("imageField").style.display = "block";
        } else if (mediaType === "video") {
            document.getElementById("videoField").style.display = "block";
        } else if (mediaType === "gif") {
            document.getElementById("gifField").style.display = "block";
        }
    }
});

// Manejador de eventos para los botones de Like y Dislike
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("like-btn") || event.target.classList.contains("dislike-btn")) {
        const postID = event.target.getAttribute("data-id");
        const isLike = event.target.classList.contains("like-btn"); // true si es like, false si es dislike
        
        fetch('./lib/update_likes.php', {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ postID, isLike })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`.like-btn[data-id="${postID}"] span`).innerText = data.likes;
                document.querySelector(`.dislike-btn[data-id="${postID}"] span`).innerText = data.dislikes;
            }
        })
        .catch(error => console.error("Error:", error));
    }
});