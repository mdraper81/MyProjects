<html>
    <head>
        <link rel="stylesheet" type="text/css" href="digitalBoard.css">
        <title>Digital Board</title>
    </head>
    <body>
        <?php
            $budgetId = $_GET['budgetId'];
        ?>
	
        <div class="container">
	    <div class="title-container">Budget Id <?php echo $budgetId ?></div>
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
