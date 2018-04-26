-- Adding in a column to specify whether money is being added or removed for a category
ALTER TABLE Categories ADD COLUMN TransactionType ENUM('Withdrawl', 'Deposit', 'Not Applicable') DEFAULT 'Not Applicable'  AFTER NAME;

-- Update the existing categories to be of type deposit (if applicable)
UPDATE Categories SET TransactionType='Deposit' WHERE CategoryId IN (2, 3, 4, 5);

-- Update the Categories table to have a few default withdrawl types
INSERT INTO Categories (CategoryId, TransactionType, Name) VALUES (6, 'Withdrawl', 'Gas'), (7, 'Withdrawl', 'Tolls'), (8, 'Withdrawl', 'Groceries'), (9, 'Withdrawl', 'Eating Out'), (10, 'Withdrawl', 'Food at Disney');
