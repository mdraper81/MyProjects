-- Submit the initial data to the database tables
INSERT INTO Budgets (BudgetId, Name) VALUES
	(1, 'Food'),
	(2, 'Misc.');

INSERT INTO Categories (CategoryId, Name) VALUES
	(1, 'Not Specified');

INSERT INTO Category_to_Budget (CategoryId, BudgetId) VALUES
	(1, 1),
	(1, 2);
