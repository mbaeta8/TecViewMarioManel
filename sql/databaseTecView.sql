CREATE DATABASE TecView;
USE TecView;

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
    resetPassCode CHAR(64)
);