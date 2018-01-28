<html>
    <head>
        <link rel="stylesheet" type="text/css" href="digitalBoard.css">
        <title>Digital Board</title>
    </head>
    <body>
        <div class="container">
 	    <div class="title-container">Digital Board</div>
            <div class="budget-container">
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
                $preparedQuery = $conn->prepare("SELECT bgt.BudgetId, bgt.Name, COALESCE(SUM(trns.Amount), 0) AS balance FROM Budgets bgt JOIN Transactions trns ON trns.BudgetId = bgt.BudgetId GROUP BY bgt.BudgetId");

                // Execute query and fetch results
                $preparedQuery->execute();
                $preparedQuery->bind_result($budgetId, $budgetName, $balance);
                while($preparedQuery->fetch())
                {
	            echo '<a href="recordTransaction.php?budgetId=' . $budgetId . '" class="budget-button"';
		    if ($balance < 0)
		    {
                        echo ' style="background-color:red;"';
		    }
		    else if ($balance < 25)
		    {
                        echo ' style="background-color:orange;"';
		    }
		    echo '>';
                    echo '    <p><b>' . $budgetName . ' Budget</b></p>';
		    echo '    <p>$' . number_format($balance, 2) . '</p>';
		    echo '</a>';
                }
                $preparedQuery->close();

                $conn->close();
            ?>
	    </div>
	    <div class="button-container">
		<a href="applyBonusFunds.php" class="styled-button">Apply Bonus Funds</a>
                <div class="styled-button">Rebalance Funds</div>
            </div>
        </div>
    </body>
</html>
