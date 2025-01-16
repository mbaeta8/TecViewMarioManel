CREATE DATABASE TecView;
USE TecView;

CREATE TABLE users (
	iduser INT IDENTITY(1,1) PRIMARY KEY,
	mail VARCHAR(40) UNIQUE,
	username VARCHAR(16) UNIQUE,
	passHash VARCHAR(60),
	userFirstName VARCHAR(60),
	userLastName VARCHAR(120),
	creationDate DATETIME,
	removeDate DATETIME,
	lastSignIn DATETIME,
	active TINYINT
);