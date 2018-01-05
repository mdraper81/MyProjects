<html>
    <head>
        <link rel="stylesheet" type="text/css" href="digitalBoard.css">
        <title>Digital Board</title>
    </head>
    <body>
        <?php
            // Load DB Connction info
            $dbConfig = parse_ini_file('dbConfig.ini');

            // Create connection
            $conn = new mysqli("localhost", $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname']);
        
            // Check connection
            if ($conn->connect_error)
            {
                die("Connection failed: " . $conn->connect_error);
	    }

	    // Record transaction if the user had submitted the form
	    if (isset($_POST['recordTx']))
	    {
                // For now all transactions go to category 'Not Specified'
                $categoryId = 1;
                $amountToDeduct = $_POST['transactionAmount'] * -1;

                // Prepare query to insert transaction into the DB
                $insertStmt = $conn->prepare("INSERT INTO Transactions (Timestamp, BudgetId, CategoryId, Amount) VALUES ( NOW(), ?, ?, ?);");
		$insertStmt->bind_param("iid", $_GET['budgetId'], $categoryId, $amountToDeduct);

		// Execute the insert
                $insertStmt->execute();
		$insertStmt->close();
	    }
	    
	    // Prepare query for budget name and balance
	    $preparedQuery = $conn->prepare("SELECT bgt.Name, COALESCE(SUM(trns.Amount), 0) AS balance FROM Budgets bgt JOIN Transactions trns ON trns.BudgetId = bgt.BudgetId WHERE bgt.BudgetId = ?");
	    $preparedQuery->bind_param("i", $_GET['budgetId']);

	    // Execute query and fetch results
	    $preparedQuery->execute();
	    $preparedQuery->bind_result($budgetName, $balance);
	    $preparedQuery->fetch();
	    $preparedQuery->close();

	    $conn->close();
        ?>
	
        <div class="container">
	    <div class="title-container"><?php echo $budgetName ?> Budget</div>
	    <div class="transaction-form">
	        <p>Current Balance: $<?php echo number_format($balance, 2); ?></p>
		<form id="transactionForm" method="post">
                    <label for="dollarAmount">Amount spent: $</label>
		    <input id="dollarAmount" name="transactionAmount" type="number" placeholder="Dollar Amount" min="0" step="0.01" size="6" required ><br>
		</form>
	    </div>
	    <div class="button-container">
                <input form="transactionForm" class="styled-button" type="submit" name="recordTx" value="Record Transaction">
                <a href="index.php" class="styled-button">Back</a>
            </div>
	</div>
    </body>
</html>
