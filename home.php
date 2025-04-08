<?php
session_start();
require './lib/controlDB.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$conn = getDBConnection();
$user = $_SESSION['user'];

$foto_perfil = 'img/default_profile.jpg';

  
$query = "SELECT userFirstName, userLastName, foto_perfil, descripcion, edad, ubicacion FROM users WHERE username = :user";

$stmt = $conn->prepare($query);
$stmt->bindParam(':user', $user);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $_SESSION['userFirstName'] = $row['userFirstName'];
    $_SESSION['userLastName'] = $row['userLastName'];
    $_SESSION['descripcion'] = $row['descripcion'];
    $_SESSION['edad'] = $row['edad'];
    $_SESSION['ubicacion'] = $row['ubicacion'];

    if (!empty($row['foto_perfil'])) {
        $imagen = $row['foto_perfil'];
        
        if (strpos($imagen, 'data:image') === 0) {
            $foto_perfil = $imagen;
        } else {
            $tipo = detectarFormatoBase64($imagen);
            $foto_perfil = "data:$tipo;base64,$imagen";
        }
    }
} else {
    echo "<pre>No se encontró imagen ni datos del usuario en la base de datos</pre>";
}

function detectarFormatoBase64($base64) {
    $inicio = substr($base64, 0, 4); 
    
    if ($inicio === '/9j/') return 'image/jpeg'; 
    if ($inicio === 'iVBOR') return 'image/png'; 
    if ($inicio === 'R0lG') return 'image/gif'; 
    
    return 'image/jpeg'; 
}

if (isset($_POST['logout'])) 
{
    session_destroy();
    header('Location: ./index.php');
    exit();
}

// Consulta mejorada para obtener posts con conteo de likes y dislikes
$queryPosts = "SELECT posts.*, users.username, users.foto_perfil, posts.createdAT, 
               (SELECT COUNT(*) FROM likes WHERE postID = posts.idPost) AS total_likes, 
               (SELECT COUNT(*) FROM dislikes WHERE postID = posts.idPost) AS total_dislikes
               FROM posts 
               JOIN users ON posts.userID = users.iduser 
               ORDER BY createdAT DESC";
$stmtPosts = $conn->prepare($queryPosts);
$stmtPosts->execute();
$posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);

// Procesar la publicación si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    // Obtener la descripción y tipo de medio
    $description = $_POST['description'];
    $mediaType = $_POST['mediaType'];
    $userId = $_SESSION['user_id']; // El ID de usuario debe estar en la sesión

    // Validar descripción y tipo de media
    if (empty($description) || $mediaType == 'none') {
        echo "Por favor complete todos los campos.";
        exit();
    }

    $mediaContent = null;
    $gifUrl = null;
    $mediaTypeEnum = null;

    // Procesar según el tipo de media seleccionado
    if ($mediaType == 'image' && isset($_FILES['image'])) {
        // Guardar imagen en base64
        $image = $_FILES['image'];
        $imageContent = base64_encode(file_get_contents($image['tmp_name']));
        $mediaContent = $imageContent;
        $mediaTypeEnum = 'image';
    } elseif ($mediaType == 'video' && isset($_FILES['video'])) {
        // Guardar video en base64
        $video = $_FILES['video'];
        $videoContent = base64_encode(file_get_contents($video['tmp_name']));
        $mediaContent = $videoContent;
        $mediaTypeEnum = 'video';
    } elseif ($mediaType == 'gif' && isset($_POST['gif'])) {
        // Guardar URL de GIF
        $gifUrl = $_POST['gif'];
        $mediaContent = $gifUrl;
        $mediaTypeEnum = 'gif_url';
    } else {
        echo "Error al cargar el archivo.";
        exit();
    }

    // Insertar la nueva publicación en la base de datos
    $query = "INSERT INTO posts (userID, content, " . ($mediaTypeEnum === 'gif_url' ? 'gif_url' : ($mediaTypeEnum === 'image' ? 'image' : 'video')) . ", media_type) VALUES (:userID, :content, :mediaContent, :mediaTypeEnum)";

    // Usar PDO en lugar de MySQLi
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userID', $userId);
    $stmt->bindParam(':content', $description);
    $stmt->bindParam(':mediaContent', $mediaContent);
    $stmt->bindParam(':mediaTypeEnum', $mediaTypeEnum);

    if ($stmt->execute()) {
        echo "Publicación creada exitosamente.";
    } else {
        echo "Error al crear la publicación.";
    }
}

?>
<!DOCTYPE html>
<html lang="es" >
    <head>
    <meta charset="UTF-8">
        <title>TecView</title>
        <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
        <link rel='stylesheet' href='./css/home.css'>
        <link rel="icon" href="./img/logo.ico">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="./js/home.js"></script>
    </head>
    <body>
        <header>
            <div class="logo" id="togglePanel">
                <img src="img/logo.png" alt="Logo">
            </div>
            <!-- Botón de añadir publicación con ícono de + -->
            <button id="addPostButton" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createPostModal">+</button>
            <!-- Modal para crear publicación -->
            <div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createPostModalLabel">Añadir publicación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="createPostForm" method="POST" action="./lib/create_post.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control" id="postDescription" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="mediaType" class="form-label">Seleccionar archivo</label>
                                <select class="form-select" id="mediaTypeSelect" name="mediaType">
                                    <option value="none">Seleccione una opción</option>
                                    <option value="image">Imagen</option>
                                    <option value="video">Video</option>
                                    <option value="gif">GIF</option>
                               </select>
                            </div>
                            
                            <!-- Input para imagen -->
                            <div id="imageField" class="mb-3" style="display:none;">
                                <label for="image" class="form-label">Subir imagen</label>
                                <input class="form-control" type="file" id="image" name="image" accept="image/*">
                            </div>

                            <!-- Input para video -->
                            <div id="videoField" class="mb-3" style="display:none;">
                                <label for="video" class="form-label">Subir video</label>
                                <input class="form-control" type="file" id="video" name="video" accept="video/*">
                            </div>

                            <!-- Input para URL de GIF -->
                            <div id="gifField" class="mb-3" style="display:none;">
                                <label for="gif" class="form-label">URL del GIF</label>
                                <input class="form-control" type="text" id="gif" name="gif">
                            </div>

                            <button type="submit" class="btn btn-primary">Publicar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <h1>Inicio</h1>
            <div class="user-menu" id="userMenu">
                <span><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                <div class="profile-icon">
                    <img id="profileImage" src="<?php echo htmlspecialchars($foto_perfil); ?>" class="rounded-circle" alt="Perfil" width="45" height="45">
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a onclick=showLoginDialog()>Perfil</a>
                    <form method="post" action="">
                        <button type="submit" name="logout" id="logout">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
            <dialog id="login-dialog">
                <div class="wrapper">
                    <div id="profile">
                        <div class="profile-icon-dialog">
                            <img id="profileImageDialog" src="<?php echo htmlspecialchars($foto_perfil); ?>" class="rounded-circle" alt="Perfil" width="45" height="45">
                        </div>
                        <div id="infoProfile">
                            <div class="form">
                                <label>Usuari: </label>
                                <input id="user" type="text" readonly placeholder="nombreUsuario" value="<?php echo htmlspecialchars($_SESSION['user']); ?>">
                            </div>
                            <div class="form">
                                <label>Nombre: </label>
                                <input id="name" type="text" readonly placeholder="nombre" value="<?php echo htmlspecialchars($_SESSION['userFirstName']); ?>">
                            </div>
                            <div class="form">
                                <label>Apellido: </label>
                                <input id="surname" type="text" readonly placeholder="apellido" value="<?php echo htmlspecialchars($_SESSION['userLastName']); ?>">
                            </div>
                            <div class="form">
                                <label>Edad: </label>
                                <input id="age" type="text" readonly placeholder="edad" value="<?php echo htmlspecialchars($_SESSION['edad']); ?>">
                            </div>
                            <div class="form" id="descriptionDiv">
                                <label id="descriptionLabel">Descripción: </label>
                                <textarea id="profileDescription" readonly placeholder="descripcion"><?php echo htmlspecialchars($_SESSION['descripcion']); ?></textarea>
                            </div>
                            <div class="form">
                                <label>Ubicacion: </label>
                                <input id="location" type="text" readonly placeholder="ubicacion" value="<?php echo htmlspecialchars($_SESSION['ubicacion']); ?>">
                            </div>
                        </div>
                        <div id="profileButton">
                            <button id="editar" onclick="editMode()">Editar</button>
                        </div>
                    </div>
                </div>
            </dialog>
        </header>
        <aside id="sidePanel" class="side-panel">
            <h3>Opciones</h3>

        <!-- Switch de modo oscuro -->
        <div class="dark-mode-container">
            <label class="theme-switch">
                <input type="checkbox" id="darkModeToggle">
                <div class="slider round"></div>
            </label>
            <span>Modo Oscuro</span>
        </div>
        </aside>
        <main>
        <!-- Publicaciones -->
        <div class="posts-container">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <!-- Cabecera de la publicación -->
                    <div class="post-header">
                        <img src="<?php echo htmlspecialchars($post['foto_perfil']); ?>" class="profile-pic">
                        <div class="userinfo">
                            <span class="name-user"><?php echo htmlspecialchars($post['username']); ?></span>
                            <span class="post-date"><?php echo htmlspecialchars($post['createdAT']); ?></span>
                        </div>
                    </div>
                    <!-- Contenido de la publicación -->
                    <div class="post-content">
                        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                        <!-- Mostrar medios según el tipo -->
                        <?php if ($post['media_type'] === 'image' && !empty($post['image'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($post['image']); ?>" class="post-media">
                        <?php elseif ($post['media_type'] === 'gif_url' && !empty($post['gif_url'])): ?>
                            <img src="<?php echo htmlspecialchars($post['gif_url']); ?>" class="post-media">
                        <?php elseif ($post['media_type'] === 'video' && !empty($post['video'])): ?>
                            <video controls class="post-media">
                                <source src="data:video/mp4;base64,<?php echo htmlspecialchars($post['video']); ?>" type="video/mp4">
                            </video>
                        <?php endif; ?>
                    </div>
                    <!-- Botones de interacción -->
                    <div class="post-actions">
                        <button class="like-btn" data-post-id="<?php echo $post['idPost']; ?>">
                            <i class="fa fa-thumbs-up"></i><span class="like-count"><?php echo $post['total_likes']; ?></span>
                        </button>
                        <button class="dislike-btn" data-post-id="<?php echo $post['idPost']; ?>">
                            <i class="fa fa-thumbs-down"></i><span class="dislike-count"><?php echo $post['total_dislikes']; ?></span>
                        </button>
                        <button class="comment-btn" data-post-id="<?php echo $post['idPost']; ?>" data-bs-toggle="modal" data-bs-target="#commentsModal-<?php echo $post['idPost']; ?>">
                            <i class="fa fa-comment"></i> Comentarios
                        </button>
                    </div>
                    <!-- Modal de comentarios -->
                    <div class="modal fade" id="commentsModal-<?php echo $post['idPost']; ?>" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">  <!-- Añadimos modal-dialog-centered -->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="commentsModalLabel">Comentarios</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="comments-<?php echo $post['idPost']; ?>" class="comments-list">
                                        <!-- Los comentarios se cargarán aquí -->
                                    </div>
                                    <div class="comment-input">
                                        <textarea id="newComment-<?php echo $post['idPost']; ?>" class="form-control" placeholder="Escribe tu comentario"></textarea>
                                        <button class="btn btn-primary mt-2" onclick="submitComment(<?php echo $post['idPost']; ?>)">Enviar comentario</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </main>
        <footer>
            <p>© 2025 TecView. Todos los derechos reservados.</p>
        </footer>
        <script>
            // Función para enviar comentario
            function submitComment(postID) {
                const commentText = document.getElementById(`newComment-${postID}`).value;
                if (commentText.trim() === '') return;

                $.post('./lib/comment.php', { postID: postID, comment: commentText }, function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        const commentsDiv = document.getElementById(`comments-${postID}`);
                        // Añadir la fecha al comentario en el HTML
                        const formattedDate = result.date; // Asegúrate de que la fecha esté incluida en la respuesta del servidor
                        commentsDiv.innerHTML += `
                            <div class="comment-item">
                                <strong>${formattedDate} - ${result.username}:</strong> ${result.comment}
                            </div>
                        `;
                        document.getElementById(`newComment-${postID}`).value = '';  // Limpiar el campo de texto
                    } else {
                        alert('Error al enviar comentario.');
                    }
                });
            }

            // Función para cargar los comentarios cuando se abra el modal
            $(document).on('show.bs.modal', '.modal', function () {
                let postID = $(this).attr('id').split('-')[1]; // Obtener el ID del post desde el modal
                let commentsDiv = $(`#comments-${postID}`);
                
                $.get('./lib/comment.php', { postID: postID }, function(response) {
                    commentsDiv.html(response);
                });
            });

            function toggleMenu(event) {
                event.stopPropagation(); 
                document.getElementById("dropdownMenu").classList.toggle("show");
            }

            document.getElementById("userMenu").addEventListener("click", toggleMenu);

            window.addEventListener("click", function(event) {
                let dropdown = document.getElementById("dropdownMenu");
                let userMenu = document.getElementById("userMenu");

                if (!userMenu.contains(event.target) && dropdown.classList.contains("show")) {
                    dropdown.classList.remove("show");
                }
            });

            const dialog = document.getElementById("login-dialog")
            const wrapper = document.querySelector(".wrapper")

            function showLoginDialog(){
                dialog.showModal()
            }

            dialog.addEventListener("click", (e) => {
                if (!wrapper.contains(e.target)) {
                    dialog.close()
                }
            })

            // Likes y Dislikes
            $(document).ready(function() {
                $(".like-btn").click(function() {
                    let postID = $(this).data("post-id");
                    let likeBtn = $(this);
                    
                    $.ajax({
                        url: "/IsitecMarioManel/lib/update_likes_dislike.php",  // Usamos la ruta completa
                        method: "POST",
                        contentType: "application/json",  // Esto asegura que los datos se envían como JSON
                        data: JSON.stringify({ postID: postID, isLike: true }),  // Convierte el objeto a JSON
                        success: function(response) {
                            let result = JSON.parse(response);
                            if (result.success) {
                                // Actualiza el conteo de likes en el botón
                                likeBtn.find(".like-count").text(result.likes);

                                // Actualizar el contador de dislikes si es necesario
                                let dislikeBtn = $(".dislike-btn[data-post-id='" + postID + "']");
                                if (dislikeBtn.length) {
                                    dislikeBtn.find(".dislike-count").text(result.dislikes);
                                }
                            } else {
                                alert(result.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert("Error en la solicitud: " + error);
                        }
                    });
                });

                $(".dislike-btn").click(function() {
                    let postID = $(this).data("post-id");
                    let dislikeBtn = $(this);
                    
                    $.ajax({
                        url: "/IsitecMarioManel/lib/update_likes_dislike.php",  // Usamos la ruta completa
                        method: "POST",
                        contentType: "application/json",  // Esto asegura que los datos se envían como JSON
                        data: JSON.stringify({ postID: postID, isLike: false }),  // Convierte el objeto a JSON
                        success: function(response) {
                            let result = JSON.parse(response);
                            if (result.success) {
                                // Actualiza el conteo de dislikes en el botón
                                dislikeBtn.find(".dislike-count").text(result.dislikes);
                            
                                // Actualizar el contador de likes si es necesario
                                let likeBtn = $(".like-btn[data-post-id='" + postID + "']");
                                if (likeBtn.length) {
                                    likeBtn.find(".like-count").text(result.likes);
                                }
                            } else {
                                alert(result.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert("Error en la solicitud: " + error);
                        }
                    });
                });
            });

            // Mostrar/ocultar campos según el tipo de medio seleccionado
            $('#createPostModal').on('hidden.bs.modal', function () {
                $('#createPostForm')[0].reset();
                $('#imageField').hide();
                $('#videoField').hide();
                $('#gifField').hide();
            });
        </script>
    </body>
</html>