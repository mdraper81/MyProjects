-- Creates initial version of the database tables for the digital board project
CREATE DATABASE IF NOT EXISTS digitalBoard;
USE digitalBoard;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS Budgets;
CREATE TABLE Budgets (
	BudgetId BIGINT NOT NULL AUTO_INCREMENT,
	Name VARCHAR(128) NOT NULL,
	PRIMARY KEY (BudgetId)
);

DROP TABLE IF EXISTS Categories;
CREATE TABLE Categories (
	CategoryId BIGINT NOT NULL AUTO_INCREMENT,
	Name VARCHAR(128) NOT NULL,
	PRIMARY KEY (CategoryId)
);

DROP TABLE IF EXISTS Category_to_Budget;
CREATE TABLE Category_to_Budget (
	CategoryId BIGINT NOT NULL,
	BudgetId BIGINT NOT NULL,
	PRIMARY KEY (CategoryId, BudgetId),
	FOREIGN KEY (CategoryId) REFERENCES Categories (CategoryId),
	FOREIGN KEY (BudgetId) REFERENCES Budgets (BudgetId)
);

DROP TABLE IF EXISTS Transactions;
CREATE TABLE Transactions (
	TransactionId BIGINT NOT NULL AUTO_INCREMENT,
	Timestamp DATETIME,
	BudgetId BIGINT,
	CategoryId BIGINT,
	Amount FLOAT,
	PRIMARY KEY (TransactionId),
	FOREIGN KEY (CategoryId) REFERENCES Categories (CategoryId),
	FOREIGN KEY (BudgetId) REFERENCES Budgets (BudgetId),
	INDEX Idx_BudgetId (BudgetId),
	INDEX Idx_BudgetId_CategoryId (BudgetId, CategoryId),
	INDEX Idx_CategoryId (CategoryId)
);

SET foreign_key_checks = 1;
