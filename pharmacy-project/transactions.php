<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

include "db_connect.php";

$message = "";
$error = "";


$canUpdateStock = ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Inventory Manager');


if ($_SERVER["REQUEST_METHOD"] == "POST" && $canUpdateStock) {
    $inventory_id = $_POST['inventory_id'];
    $transaction_type = $_POST['transaction_type'];
    $amount = (int) $_POST['amount_changed'];
    $user_id = $_SESSION['user_id'];

    if ($amount <= 0) {
        $error = "Amount must be greater than 0.";
    } else {
        if ($transaction_type == "Stock Out") {
            $amount_changed = $amount * -1;
        } else {
            $amount_changed = $amount;
        }

        try {
            $conn->begin_transaction();

            /*
                Check current quantity first.
                This prevents Stock Out from making inventory negative.
            */
            $checkSql = "SELECT quantity FROM inventory WHERE inventory_id = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("i", $inventory_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows == 0) {
                throw new Exception("Inventory item not found.");
            }

            $inventoryRow = $checkResult->fetch_assoc();
            $currentQuantity = $inventoryRow['quantity'];

            if ($transaction_type == "Stock Out" && abs($amount_changed) > $currentQuantity) {
                throw new Exception("Stock Out amount cannot be greater than the current quantity.");
            }


            $insertSql = "INSERT INTO transactions 
                          (inventory_id, user_id, transaction_type, amount_changed, transaction_date)
                          VALUES (?, ?, ?, ?, NOW())";

            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iisi", $inventory_id, $user_id, $transaction_type, $amount_changed);
            $insertStmt->execute();


            $updateSql = "UPDATE inventory
                          SET quantity = quantity + ?
                          WHERE inventory_id = ?";

            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ii", $amount_changed, $inventory_id);
            $updateStmt->execute();

            $conn->commit();

            $message = "Transaction recorded successfully and inventory quantity updated.";

        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}


$inventorySql = "SELECT 
                    i.inventory_id,
                    m.medication_name,
                    i.quantity,
                    i.expiration_date,
                    i.location
                 FROM inventory i
                 JOIN medications m 
                    ON i.medication_id = m.medication_id
                 ORDER BY m.medication_name ASC";

$inventoryResult = $conn->query($inventorySql);


$transactionSql = "SELECT 
                    t.transaction_id,
                    m.medication_name,
                    t.transaction_type,
                    t.amount_changed,
                    t.transaction_date,
                    u.first_name,
                    u.last_name
                   FROM transactions t
                   JOIN inventory i 
                    ON t.inventory_id = i.inventory_id
                   JOIN medications m 
                    ON i.medication_id = m.medication_id
                   JOIN users u 
                    ON t.user_id = u.user_id
                   ORDER BY t.transaction_date DESC
                   LIMIT 25";

$transactionResult = $conn->query($transactionSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #eef6f8;
        }

        .header {
            background-color: #0b5c75;
            color: white;
            padding: 22px;
            text-align: center;
        }

        .nav {
            background-color: #08475c;
            padding: 13px;
            text-align: center;
        }

        .nav a {
            color: white;
            text-decoration: none;
            margin: 0 16px;
            font-weight: bold;
        }

        .nav a:hover {
            text-decoration: underline;
        }

        .container {
            width: 90%;
            margin: 30px auto;
        }

        .section-box {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.15);
            margin-bottom: 30px;
        }

        .page-title {
            color: #0b5c75;
            margin-top: 0;
            margin-bottom: 5px;
        }

        .description {
            color: #555;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .role-note {
            margin-bottom: 20px;
            background-color: #e8f4f8;
            padding: 12px;
            border-left: 5px solid #0b5c75;
            color: #444;
            border-radius: 5px;
        }

        .message {
            background-color: #e6f4ea;
            color: green;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error {
            background-color: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-row {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
        }

        select,
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #aaa;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
        }

        .btn {
            background-color: #0b5c75;
            color: white;
            padding: 11px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            font-size: 15px;
        }

        .btn:hover {
            background-color: #08475c;
        }

        .restricted-box {
            background-color: #fff8e6;
            border-left: 5px solid #f0ad4e;
            padding: 15px;
            color: #555;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #0b5c75;
            color: white;
            padding: 12px;
        }

        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            color: #444;
        }

        tr:hover {
            background-color: #f2f8fa;
        }

        .stock-in {
            color: green;
            font-weight: bold;
        }

        .stock-out {
            color: #d9534f;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Pharmacy Medication Tracking System</h1>
    </div>

    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="medications.php">Medications</a>
        <a href="inventory.php">Inventory</a>
        <a href="transactions.php">Transactions</a>
        <a href="alerts.php">Alerts</a>
		<?php if ($_SESSION['role'] != 'Technician') { ?>
			<a href="reports.php">Reports</a>
		<?php } ?>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">

        <div class="role-note">
            Logged in as: <strong><?php echo $_SESSION['role']; ?></strong>
        </div>

        <div class="section-box">
            <h2 class="page-title">Record Stock Transaction</h2>
            <p class="description">
                Enter Stock In or Stock Out activity. This page records the transaction and updates the inventory quantity.
            </p>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <?php if ($canUpdateStock) { ?>

                <form action="transactions.php" method="post">

                    <div class="form-row">
                        <label for="inventory_id">Medication / Inventory Item</label>
                        <select id="inventory_id" name="inventory_id" required>
                            <option value="">Select inventory item...</option>

                            <?php while ($item = $inventoryResult->fetch_assoc()) { ?>
                                <option value="<?php echo $item['inventory_id']; ?>">
                                    <?php 
                                        echo $item['medication_name'] . 
                                             " | Qty: " . $item['quantity'] . 
                                             " | Exp: " . $item['expiration_date'] . 
                                             " | " . $item['location']; 
                                    ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                    <div class="form-row">
                        <label for="transaction_type">Transaction Type</label>
                        <select id="transaction_type" name="transaction_type" required>
                            <option value="">Select transaction type...</option>
                            <option value="Stock In">Stock In</option>
                            <option value="Stock Out">Stock Out</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label for="amount_changed">Amount</label>
                        <input type="number" id="amount_changed" name="amount_changed" min="1" placeholder="Enter quantity amount" required>
                    </div>

                    <button type="submit" class="btn">Save Transaction</button>

                </form>

            <?php } else { ?>

                <div class="restricted-box">
                    You can view transaction history, but only Admin and Inventory Manager users can enter new stock transactions.
                </div>

            <?php } ?>

        </div>

        <div class="section-box">
            <h2 class="page-title">Recent Transaction History</h2>
            <p class="description">
                The most recent Stock In and Stock Out records are shown below.
            </p>

            <table>
                <tr>
                    <th>Transaction ID</th>
                    <th>Medication Name</th>
                    <th>Type</th>
                    <th>Amount Changed</th>
                    <th>Date</th>
                    <th>User</th>
                </tr>

                <?php while ($row = $transactionResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['transaction_id']; ?></td>
                        <td><?php echo $row['medication_name']; ?></td>

                        <td>
                            <?php if ($row['transaction_type'] == 'Stock In') { ?>
                                <span class="stock-in">Stock In</span>
                            <?php } else { ?>
                                <span class="stock-out">Stock Out</span>
                            <?php } ?>
                        </td>

                        <td><?php echo $row['amount_changed']; ?></td>
                        <td><?php echo $row['transaction_date']; ?></td>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </div>

</body>
</html>

<?php
$conn->close();
?>