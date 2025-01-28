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
	activat TINYINT(1)
);

INSERT INTO users (mail,username,passHash,userFirstName,userLastName,creationDate,activat) VALUES ("user1234@gmail.com","user1234","1234","Paco","Franchesco",CURDATE(),1);