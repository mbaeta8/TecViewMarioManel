CREATE DATABASE TecView;
USE TecView;

DROP DATABASE TecView;

CREATE TABLE users (
	iduser INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	mail VARCHAR(40) NOT NULL UNIQUE,
	username VARCHAR(16) NOT NULL UNIQUE,
	passHash VARCHAR(60) NOT NULL,
	userFirstName VARCHAR(60),
	userLastName VARCHAR(120),
	creationDate DATETIME,
	removeDate DATETIME,
	lastSignIn DATETIME,
	activat TINYINT(1),
    activationDate DATETIME,
    activationCode CHAR(64),
    resetPassExpiry DATETIME,
    resetPassCode CHAR(64),
    foto_perfil LONGTEXT NULL,
    banner LONGTEXT NULL,
    descripcion TEXT,
	edad INT,
    ubicacion VARCHAR(100) NULL
);

CREATE TABLE posts (
	idPost INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    content TEXT NOT NULL,
    image LONGTEXT NULL,
    createdAT DATETIME DEFAULT CURRENT_TIMESTAMP,
    likes INT DEFAULT 0,
    FOREIGN KEY (userID) REFERENCES users(iduser) ON DELETE CASCADE
);