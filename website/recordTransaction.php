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
	    
	    // Prepare query for budget name and balance
	    $preparedQuery = $conn->prepare("SELECT Name FROM Budgets WHERE BudgetId = ?");
	    $preparedQuery->bind_param("i", $_GET['budgetId']);

	    // Execute query and fetch results
	    $preparedQuery->execute();
	    $preparedQuery->bind_result($budgetName);
	    $preparedQuery->fetch();
	    $preparedQuery->close();

	    $conn->close();
        ?>
	
        <div class="container">
	    <div class="title-container"><?php echo $budgetName ?> Budget</div>
	    <div class="transaction-form">
		<p>Current Balance: $127.00</p>
                <form id="transactionForm">
                    <label for="dollarAmount">Amount spent: $</label>
		    <input id="dollarAmount" type="number" placeholder="Dollar Amount" min="0" step="0.01" size="6" required ><br>
		</form>
	    </div>
	    <div class="button-container">
                <input form="transactionForm" class="styled-button" type="submit" value="Record Transaction">
                <a href="index.php" class="styled-button">Cancel</a>
            </div>
	</div>
    </body>
</html>
