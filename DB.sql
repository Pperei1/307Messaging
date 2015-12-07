CREATE DATABASE FPro;
CREATE TABLE Users (
	memberID INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(30) NOT NULL,
	password VARCHAR(30) NOT NULL,
)

CREATE TABLE Session(
	id INT(6),
<<<<<<< HEAD
	sessionID(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
=======
	sessionID(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	FOREIGN KEY(id) REFERENCES Users(id)
>>>>>>> origin/master
)

CREATE TABLE Messages(
	senderID INT(6),
	receiverID INT(6),
	message VARCHAR(255),
<<<<<<< HEAD
	c INT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	PRIMARY KEY(senderID,receiverID)
=======
	PRIMARY KEY(senderID,receiverID),
	FOREIGN KEY(senderID) REFERENCES Users(id),
	FOREIGN KEY(receiverID) REFERENCES Users(id)
>>>>>>> origin/master
)
	