-- Create a table to store various periods that money can be distributed and populate it
DROP TABLE IF EXISTS Periods;
CREATE TABLE Periods (
	PeriodId INT NOT NULL,
	Description VARCHAR(64) NOT NULL,
	PreviousTimestamp DATETIME,
	NextTimestamp DATETIME,
	PRIMARY KEY (PeriodId)
);

INSERT INTO Periods (PeriodId, Description) VALUES (0, 'None'), (1, 'Weekly'), (2, 'Biweekly'), (3, 'Monthly'), (4, 'Quarterly'), (5, 'Semi-Annual'), (6, 'Annual');

-- Add more information to the Budgets table to define how much money should be distributed and with
-- what frequency.  Populate this table with the appropriate info
ALTER TABLE Budgets ADD COLUMN DistributionAmount INT NOT NULL DEFAULT 0 AFTER Name;
ALTER TABLE Budgets ADD COLUMN PeriodId INT NOT NULL DEFAULT 0 AFTER DistributionAmount;
ALTER TABLE Budgets ADD CONSTRAINT fk_Budgets_PeriodId FOREIGN KEY (PeriodId) REFERENCES Periods (PeriodId);

UPDATE Budgets SET PeriodId = 2, DistributionAmount = 300 WHERE BudgetId = 1;
UPDATE Budgets SET PeriodId = 2, DistributionAmount = 330 WHERE BudgetId = 2;

-- Stored procedure to be called when a periodic distribution should be run
DROP PROCEDURE IF EXISTS SP_BUDGET_DISTRIBUTION;
DELIMITER $$
CREATE PROCEDURE SP_BUDGET_DISTRIBUTION(_PeriodId INT)
BEGIN
    DECLARE _done INT DEFAULT FALSE;
    DECLARE _budgetId INT;
	DECLARE _amount INT;
	DECLARE _NextTimestamp TIMESTAMP;
    DECLARE distributionCursor CURSOR FOR SELECT BudgetId, DistributionAmount FROM Budgets WHERE PeriodId = _PeriodId;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;
	
	SET @PAYCHECK_CATEGORY_ID = 2;
    
	START TRANSACTION;
	
    OPEN distributionCursor;
	
	distributionLoop: LOOP
	    FETCH distributionCursor INTO _budgetId, _amount;
		
		IF _done THEN
		    LEAVE distributionLoop;
	    END IF;
		
		INSERT INTO Transactions (Timestamp, BudgetId, CategoryId, Amount) VALUES (NOW(), _budgetId, @PAYCHECK_CATEGORY_ID, _amount);
	END LOOP;
	
	CLOSE distributionCursor;
	
	CASE _PeriodId
	  WHEN 1 THEN SET _NextTimestamp = NOW() + INTERVAL 1 WEEK;
	  WHEN 2 THEN SET _NextTimestamp = NOW() + INTERVAL 2 WEEK;
	  WHEN 3 THEN SET _NextTimestamp = NOW() + INTERVAL 1 MONTH;
	  WHEN 4 THEN SET _NextTimestamp = NOW() + INTERVAL 3 MONTH;
	  WHEN 5 THEN SET _NextTimestamp = NOW() + INTERVAL 6 MONTH;
	  WHEN 6 THEN SET _NextTimestamp = NOW() + INTERVAL 1 YEAR;
	  ELSE SET _NextTimestamp = NOW();
	END CASE;
	
	UPDATE Periods SET PreviousTimestamp = NOW(), NextTimeStamp = _NextTimestamp WHERE PeriodId = _PeriodId;
	
	COMMIT;
END$$
DELIMITER ;

-- Create events to periodically call the distribute paycheck stored procedure
CREATE EVENT IF NOT EXISTS digitalBoard.event_weekly_budget_distribution
ON SCHEDULE EVERY 1 WEEK STARTS '2018-02-02 00:00:00' 
DO CALL SP_BUDGET_DISTRIBUTION(1);

CREATE EVENT IF NOT EXISTS digitalBoard.event_biweekly_budget_distribution
ON SCHEDULE EVERY 2 WEEK STARTS '2018-02-02 00:00:00' 
DO CALL SP_BUDGET_DISTRIBUTION(2);

CREATE EVENT IF NOT EXISTS digitalBoard.event_monthly_budget_distribution
ON SCHEDULE EVERY 1 MONTH STARTS '2018-02-01 00:00:00' 
DO CALL SP_BUDGET_DISTRIBUTION(3);

CREATE EVENT IF NOT EXISTS digitalBoard.event_quarterly_budget_distribution
ON SCHEDULE EVERY 1 MONTH STARTS '2018-04-01 00:00:00' 
DO CALL SP_BUDGET_DISTRIBUTION(4);

CREATE EVENT IF NOT EXISTS digitalBoard.event_semiannual_budget_distribution
ON SCHEDULE EVERY 1 MONTH STARTS '2018-07-01 00:00:00' 
DO CALL SP_BUDGET_DISTRIBUTION(5);

CREATE EVENT IF NOT EXISTS digitalBoard.event_annual_budget_distribution
ON SCHEDULE EVERY 1 MONTH STARTS '2019-01-01 00:00:00' 
DO CALL SP_BUDGET_DISTRIBUTION(6);
