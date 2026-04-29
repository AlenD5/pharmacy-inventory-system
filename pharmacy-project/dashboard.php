<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

include "db_connect.php";

$totalMedicationsSql = "SELECT COUNT(*) AS total FROM medications";
$totalMedicationsResult = $conn->query($totalMedicationsSql);
$totalMedications = $totalMedicationsResult->fetch_assoc()['total'];

$lowStockSql = "SELECT COUNT(*) AS total
                FROM inventory i
                JOIN medications m ON i.medication_id = m.medication_id
                WHERE i.quantity < m.reorder_threshold";
$lowStockResult = $conn->query($lowStockSql);
$lowStock = $lowStockResult->fetch_assoc()['total'];

$expiringSoonSql = "SELECT COUNT(*) AS total
                    FROM inventory
                    WHERE expiration_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)";
$expiringSoonResult = $conn->query($expiringSoonSql);
$expiringSoon = $expiringSoonResult->fetch_assoc()['total'];

$transactionsSql = "SELECT COUNT(*) AS total FROM transactions";
$transactionsResult = $conn->query($transactionsSql);
$totalTransactions = $transactionsResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Dashboard</title>

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

        .header h1 {
            margin: 0;
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
            width: 88%;
            margin: 30px auto;
        }

        .welcome-section {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.15);
            margin-bottom: 25px;
        }

        .welcome-section h2 {
            color: #0b5c75;
            margin-top: 0;
        }

        .welcome-section p {
            color: #555;
            line-height: 1.5;
        }

        .role-badge {
            display: inline-block;
            background-color: #e8f4f8;
            color: #0b5c75;
            padding: 7px 12px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 5px;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .card {
            background-color: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.15);
            text-align: center;
        }

        .card h3 {
            color: #0b5c75;
            margin-bottom: 8px;
        }

        .card p {
            color: #555;
            font-size: 14px;
        }

        .card-number {
            font-size: 28px;
            font-weight: bold;
            color: #08475c;
            margin: 10px 0;
        }

        .quick-actions {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.15);
        }

        .quick-actions h2 {
            color: #0b5c75;
            margin-top: 0;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .action-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 18px;
            background-color: #f9fcfd;
        }

        .action-box h3 {
            color: #0b5c75;
            margin-top: 0;
        }

        .action-box p {
            color: #555;
            font-size: 14px;
        }

        .action-box a {
            display: inline-block;
            background-color: #0b5c75;
            color: white;
            padding: 9px 13px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 8px;
        }

        .action-box a:hover {
            background-color: #08475c;
        }

        .note {
            margin-top: 25px;
            background-color: #fff8e6;
            border-left: 5px solid #f0ad4e;
            padding: 15px;
            color: #555;
            border-radius: 5px;
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

        <div class="welcome-section">
            <h2>Welcome, <?php echo $_SESSION['first_name'] . " " . $_SESSION['last_name']; ?></h2>

            <p>
                This dashboard gives you quick access to medication records, inventory levels,
                stock alerts, transactions, and reports.
            </p>

            <span class="role-badge">
                Role: <?php echo $_SESSION['role']; ?>
            </span>
        </div>

        <div class="summary-cards">
            <div class="card">
                <h3>Medications</h3>
                <div class="card-number"><?php echo $totalMedications;?></div>
                <p>Total medication records will appear here.</p>
            </div>

            <div class="card">
                <h3>Low Stock</h3>
                <div class="card-number"><?php echo $lowStock;?></div>
                <p>Medications below reorder threshold.</p>
            </div>

            <div class="card">
                <h3>Expiring Soon</h3>
                <div class="card-number"><?php echo $expiringSoon;?></div>
                <p>Inventory close to expiration date.</p>
            </div>

            <div class="card">
                <h3>Transactions</h3>
                <div class="card-number"><?php echo $totalTransactions;?></div>
                <p>Recent stock in and stock out activity.</p>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Quick Access</h2>

            <div class="actions-grid">

                <div class="action-box">
                    <h3>Medications</h3>
                    <p>View medication names, categories, descriptions, suppliers, and reorder thresholds.</p>
                    <a href="medications.php">Open Medications</a>
                </div>

                <div class="action-box">
                    <h3>Inventory</h3>
                    <p>View current stock quantity, expiration dates, and storage locations.</p>
                    <a href="inventory.php">Open Inventory</a>
                </div>

                <div class="action-box">
                    <h3>Transactions</h3>
                    <p>Record Stock In and Stock Out changes and update inventory quantities.</p>
                    <a href="transactions.php">Open Transactions</a>
                </div>

                <div class="action-box">
                    <h3>Alerts</h3>
                    <p>Check low-stock medications and medications that are expiring soon.</p>
                    <a href="alerts.php">Open Alerts</a>
                </div>

				<?php if ($_SESSION['role'] != 'Technician') { ?>
					<div class="action-box">
						<h3>Reports</h3>
						<p>Analyze most-used medications, monthly usage trends, and high-risk medications.</p>
						<a href="reports.php">Open Reports</a>
					</div>
				<?php } ?>

                <?php if ($_SESSION['role'] == 'Pharmacist') { ?>
                    <div class="action-box">
                        <h3>Pharmacist Tools</h3>
                        <p>Pharmacists can add, edit, and manage medication and inventory records.</p>
                        <a href="medications.php">Manage Records</a>
                    </div>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'Technician') { ?>
                    <div class="action-box">
                        <h3>Technician Access</h3>
                        <p>Technicians can view medications, check inventory, and record basic stock activity.</p>
                        <a href="inventory.php">View Inventory</a>
                    </div>
                <?php } ?>
				<?php if ($_SESSION['role'] == 'Inventory Manager') { ?>
					<div class="action-box">
						<h3>Inventory Manager Tools</h3>
						<p>Inventory managers can monitor stock levels, update inventory, and handle reorder activity.</p>
						<a href="inventory.php">Manage Inventory</a>
					</div>
				<?php } ?>
				<?php if ($_SESSION['role'] == 'Admin') { ?>
					<div class="action-box">
						<h3>Manage Users</h3>
						<p>Create new users, assign roles, and manage access to the pharmacy system.</p>
						<a href="manageUsers.php">Open Manage Users</a>
					</div>
				<?php } ?>

            </div>
        </div>

        <div class="note">
            <strong>Reminder:</strong> The dashboard is only the home page. Adding medications,
            updating inventory, entering transactions, and viewing alerts should happen on their own pages.
        </div>

    </div>

</body>
</html>
<?php
$conn->close();
?>