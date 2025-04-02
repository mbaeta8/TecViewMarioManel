CREATE DATABASE TecView;
USE TecView;

DROP DATABASE IF EXISTS TecView;

CREATE TABLE users (
    iduser INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(40) NOT NULL UNIQUE,
    username VARCHAR(16) NOT NULL UNIQUE,
    passHash VARCHAR(60) NOT NULL,
    userFirstName VARCHAR(60),
    userLastName VARCHAR(120),
    creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    removeDate DATETIME NULL,
    lastSignIn DATETIME NULL,
    activat TINYINT(1),
    activationDate DATETIME NULL,
    activationCode CHAR(64) NULL,
    resetPassExpiry DATETIME NULL,
    resetPassCode CHAR(64) NULL,
    foto_perfil LONGTEXT NULL,
    banner LONGTEXT NULL,
    descripcion TEXT NULL,
    edad INT NULL,
    ubicacion VARCHAR(100) NULL
);

CREATE TABLE posts (
    idPost INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    content TEXT NOT NULL,
    image LONGTEXT NULL,  -- Para almacenar imÃ¡genes en base64
	gif_url VARCHAR(255) NULL, -- Para almacenar URLs de GIFs
    video LONGTEXT NULL,  -- Para almacenar videos en base64
    media_type ENUM('image','gif_url','video') NULL, -- Tipo de archivo multimedia
    createdAT DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES users(iduser) ON DELETE CASCADE,
    CHECK (
	(image IS NOT NULL AND gif_url IS NULL AND video IS NULL AND media_type = 'image') OR
        (video IS NOT NULL AND image IS NULL AND gif_url IS NULL AND media_type = 'video') OR
        (gif_url IS NOT NULL AND image IS NULL AND video IS NULL AND media_type = 'gif_url') OR
        (image IS NULL AND gif_url IS NULL AND video IS NULL AND media_type IS NULL) -- Solo uno o ninguno
    )
);

CREATE TABLE likes (
    idLike INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    postID INT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (userID, postID),  -- Un usuario solo puede dar like una vez por post
    FOREIGN KEY (userID) REFERENCES users(iduser) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES posts(idPost) ON DELETE CASCADE
);

CREATE TABLE dislikes (
    idDislike INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    postID INT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (userID, postID),  -- Un usuario solo puede dar dislike una vez por post
    FOREIGN KEY (userID) REFERENCES users(iduser) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES posts(idPost) ON DELETE CASCADE
);

CREATE TABLE comments (
    idComment INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    postID INT NOT NULL,
    commentario TEXT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES users(iduser) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES posts(idPost) ON DELETE CASCADE
);