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
                $categoryId = $_POST['transactionCategory'];
                $amountToDeduct = $_POST['transactionAmount'] * -1;

                echo 'Recording transaction of $' . $amountToDeduct . ' with category id ' . $categoryId;

                // Prepare query to insert transaction into the DB
                $insertStmt = $conn->prepare("INSERT INTO Transactions (Timestamp, BudgetId, CategoryId, Amount) VALUES ( NOW(), ?, ?, ?);");
                $insertStmt->bind_param("iid", $_GET['budgetId'], $categoryId, $amountToDeduct);

                // Execute the insert
                $insertStmt->execute();
                $insertStmt->close();
            }
        
            // Prepare query for budget name and balance
	        if ($preparedQuery = $conn->prepare("SELECT bgt.Name, COALESCE(SUM(trns.Amount), 0) AS balance FROM Budgets bgt JOIN Transactions trns ON trns.BudgetId = bgt.BudgetId WHERE bgt.BudgetId = ?"))
            {
                $preparedQuery->bind_param("i", $_GET['budgetId']);
                $preparedQuery->execute();
                $preparedQuery->bind_result($budgetName, $balance);
                $preparedQuery->fetch();
                $preparedQuery->close();
            }

            // Build the array of transaction categories available to this budget
	        $categoryList = array();
            $selectedCategoryId = 0;
            if ($preparedCategoryQuery = $conn->prepare("SELECT ctg.CategoryId, ctg.Name FROM Category_to_Budget ctgToBgt JOIN Categories ctg ON ctgToBgt.CategoryId = ctg.CategoryId WHERE ctgToBgt.BudgetId = ?"))
	        {
                $preparedCategoryQuery->bind_param("i", $_GET['budgetId']);
                $preparedCategoryQuery->execute();
                $preparedCategoryQuery->bind_result($ctgId, $ctgName);

                while ($preparedCategoryQuery->fetch())
                {
                    $categoryList[ $ctgId ] = $ctgName;
                }

                $preparedCategoryQuery->close();
            }

            $conn->close();
        ?>
    
        <div class="container">
        <div class="title-container"><?php echo $budgetName ?> Budget</div>
        <div class="transaction-form">
            <p>Current Balance: $<?php echo number_format($balance, 2); ?></p>
            <form id="transactionForm" method="post">
                <label for="dollarAmount">Amount spent: $</label>
                <input id="dollarAmount" name="transactionAmount" type="number" placeholder="Dollar Amount" min="0" step="0.01" size="6" required ><br>
                <p>Category:       
                    <?php
                        echo '                        <select id="transaction" name="transactionCategory" class="styled-dropdown" style="width:50%;">';
                        foreach ($categoryList as $key => $value)
                        {
                            $selectedTagText = "";
                            if ($key == $selectedCategoryId)
                            {
			                    $selectedTagText = " selected";
                            }
		
			                echo '                            <option value="' . $key . '"' . $selectedTagText . '>' . $value . '</option>';
                        }
        
                        echo '                        </select>';
                    ?>
                </p>
            </form>
        </div>
        <div class="button-container">
                <input form="transactionForm" class="styled-button" type="submit" name="recordTx" value="Record Transaction">
                <a href="index.php" class="styled-button">Back</a>
            </div>
    </div>
    </body>
</html>
