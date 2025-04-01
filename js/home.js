// Declaración global de isEditing y originalValues
let isEditing = false;
let originalValues = {};

document.addEventListener("DOMContentLoaded", function () {
    // Variables y configuraciones iniciales
    let panel = document.getElementById("sidePanel");
    let logo = document.getElementById("togglePanel");
    let darkModeButton = document.getElementById("darkModeToggle");
    let sunIcon = document.getElementById("sunIcon");
    let moonIcon = document.getElementById("moonIcon");
    const profileImage = document.getElementById("profileImage");
    const profileImageDialog = document.getElementById("profileImageDialog");
    const createPostForm = document.getElementById("createPostForm");
    const mediaTypeSelect = document.getElementById("mediaType");

    // Panel lateral
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

    // Cambiar la imagen del perfil
    function handleImageChange(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                profileImage.src = e.target.result;
                profileImageDialog.src = e.target.result;
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

    // Crear publicación
    createPostForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const description = document.getElementById("description").value.trim();
        const mediaType = mediaTypeSelect.value;
        const image = document.getElementById("image").files[0];
        const video = document.getElementById("video").files[0];
        const gifUrl = document.getElementById("gif").value.trim();

        if (description === "") {
            alert("Por favor ingrese una descripción.");
            return;
        }

        if (mediaType === "none") {
            alert("Por favor seleccione un tipo de medio.");
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
            formData.append("video", video);
        }

        if (mediaType === "gif" && gifUrl) {
            formData.append("gifUrl", gifUrl);
        }

        fetch('./lib/create_post.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');

            // Primero, leer la respuesta como texto para ver su contenido
            return response.text(); // Cambiar a text() para inspeccionar el contenido
        })
        .then(data => {
            console.log("Respuesta del servidor:", data); // Ver el contenido de la respuesta

            // Verificar si la respuesta no esta vacia
            if (data.trim() === "") 
            {
                throw new Error('La respuesta del servidor está vacía');
            }

            // Intentar parsear el JSON si no está vacío
            try {
                const jsonData = JSON.parse(data);

                if (jsonData.success) {
                    alert("Publicación creada exitosamente");
                    addPostToFeed(jsonData.post);
                    createPostForm.reset();
                } else {
                    alert("Hubo un error al crear la publicación.");
                    console.error(jsonData.message);
                }
            } catch (error) {
                console.error("Error al parsear la respuesta:", error);
                alert("Hubo un problema al procesar la respuesta del servidor.");
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
            alert("Hubo un error al intentar crear la publicación.");
        });
    });

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

        // Ajustar scroll para que la publicación más nueva quede en el centro
        setTimeout(() => {
            postElement.scrollIntoView({ behavior: "smooth", block: "center" });
        }, 300);
    }    

    // Cambiar campos de medios según el tipo seleccionado
    mediaTypeSelect.addEventListener("change", function () {
        toggleMediaFields();
    });

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

    toggleMediaFields(); // Inicializar el estado de los campos de medios

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
});