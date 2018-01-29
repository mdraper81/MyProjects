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

	    // Build the array of budget ids and budget names available
	    $budgetList = array();
	    $budgetListQuery = "SELECT BudgetId, Name FROM Budgets;";
	    $budgetListResult = $conn->query($budgetListQuery);
	    if ($budgetListResult->num_rows > 0)
	    {
		while ($row = $budgetListResult->fetch_assoc())
		{
		    $budgetList[ $row["BudgetId"] ] = $row["Name"];
		}
	    }

	    // If a buget is selected, go ahead and query the current balance of that budget
	    $balance = 0;
	    if (count($budgetList) > 0)
	    {
		// The selected budget is the first one in the list unless we set a specific budget id
		// in the post
		$selectedBudgetId = key($budgetList);
		if (isset($_POST['selectedBudgetId']))
		{
		    $selectedBudgetId = $_POST['selectedBudgetId'];
		}

                // Record transaction if the user had submitted the form
	        if (isset($_POST['applyFunds']))
                {
                    // All transactions go to category 'Bonus Funds'
                    $categoryId = 4;
                    $amountToAdd = $_POST['transactionAmount'];

                    // Prepare query to insert transaction into the DB
                    $insertStmt = $conn->prepare("INSERT INTO Transactions (Timestamp, BudgetId, CategoryId, Amount) VALUES ( NOW(), ?, ?, ?);");
	            $insertStmt->bind_param("iid", $selectedBudgetId, $categoryId, $amountToAdd);

                    // Execute the insert
                    $insertStmt->execute();
                    $insertStmt->close();
	        }

	        // Prepare query for budget name and balance
                $preparedQuery = $conn->prepare("SELECT COALESCE(SUM(trns.Amount), 0) AS balance FROM Budgets bgt JOIN Transactions trns ON trns.BudgetId = bgt.BudgetId WHERE bgt.BudgetId = ?");
                $preparedQuery->bind_param("i", $selectedBudgetId);

                // Execute query and fetch results
	        $preparedQuery->execute();
                $preparedQuery->bind_result($balance);
                $preparedQuery->fetch();
                $preparedQuery->close();
	    }

	    $conn->close();
        ?>
	
        <div class="container">
	    <div class="title-container">Apply Bonus Funds</div>
            <div class="transaction-form">
		<form id="applyFundsForm" method="post">
		    <p>Selected Budget:
                        <?php
                            echo '                        <select name="selectedBudgetId" class="styled-dropdown" style="width:50%;" onchange="this.form.submit()">';
	                    foreach ($budgetList as $key => $value)
			    {
		                $selectedTagText = "";
				if ($key == $selectedBudgetId)
				{
				    $selectedTagText = " selected";
				}
			        echo '                            <option value="' . $key . '"' . $selectedTagText . '>' . $value . '</option>';
			    }
			    echo '                        </select>';
			?>
                    </p>
                    <p>Current Balance: $<?php echo number_format($balance, 2); ?></p>
                    <label for="dollarAmount">Amount spent: $</label>
		    <input id="dollarAmount" name="transactionAmount" type="number" placeholder="Dollar Amount" min="0" step="0.01" size="6" required ><br>
		</form>
	    </div>
	    <div class="button-container">
                <input form="applyFundsForm" class="styled-button" type="submit" name="applyFunds" value="Apply Funds">
                <a href="index.php" class="styled-button">Back</a>
            </div>
	</div>
    </body>
</html>
