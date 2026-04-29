<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

include "db_connect.php";

$lowStockSql = "SELECT 
                    i.inventory_id,
                    m.medication_name,
                    m.category,
                    i.quantity,
                    m.reorder_threshold,
                    i.location
                FROM inventory i
                JOIN medications m 
                    ON i.medication_id = m.medication_id
                WHERE i.quantity < m.reorder_threshold
                ORDER BY i.quantity ASC";

$lowStockResult = $conn->query($lowStockSql);


$expiringSoonSql = "SELECT 
                        i.inventory_id,
                        m.medication_name,
                        m.category,
                        i.quantity,
                        i.expiration_date,
                        i.location
                    FROM inventory i
                    JOIN medications m 
                        ON i.medication_id = m.medication_id
                    WHERE i.expiration_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
                    ORDER BY i.expiration_date ASC";

$expiringSoonResult = $conn->query($expiringSoonSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Alerts</title>

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

        .alert-low {
            color: #d98c00;
            font-weight: bold;
        }

        .alert-expire {
            color: #d9534f;
            font-weight: bold;
        }

        .empty-message {
            background-color: #e6f4ea;
            color: green;
            padding: 15px;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn {
            background-color: #0b5c75;
            color: white;
            padding: 9px 13px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #08475c;
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
            <h2 class="page-title">Low Stock Alerts</h2>
            <p class="description">
                These medications are below their reorder threshold and may need attention.
            </p>

            <?php if ($lowStockResult->num_rows > 0) { ?>
                <table>
                    <tr>
                        <th>Inventory ID</th>
                        <th>Medication Name</th>
                        <th>Category</th>
                        <th>Current Quantity</th>
                        <th>Reorder Threshold</th>
                        <th>Location</th>

                        <?php if ($_SESSION['role'] == 'Inventory Manager' || $_SESSION['role'] == 'Admin') { ?>
                            <th>Action</th>
                        <?php } ?>
                    </tr>

                    <?php while ($row = $lowStockResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['inventory_id']; ?></td>
                            <td><?php echo $row['medication_name']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td class="alert-low"><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['reorder_threshold']; ?></td>
                            <td><?php echo $row['location']; ?></td>

                            <?php if ($_SESSION['role'] == 'Inventory Manager' || $_SESSION['role'] == 'Admin') { ?>
                                <td>
                                    <a class="btn" href="transactions.php">Update Stock</a>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p class="empty-message">No low-stock medications found.</p>
            <?php } ?>
        </div>

        <div class="section-box">
            <h2 class="page-title">Expiring Soon Alerts</h2>
            <p class="description">
                These medications are expiring within the next 90 days.
            </p>

            <?php if ($expiringSoonResult->num_rows > 0) { ?>
                <table>
                    <tr>
                        <th>Inventory ID</th>
                        <th>Medication Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Expiration Date</th>
                        <th>Location</th>
                    </tr>

                    <?php while ($row = $expiringSoonResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['inventory_id']; ?></td>
                            <td><?php echo $row['medication_name']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td class="alert-expire"><?php echo $row['expiration_date']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p class="empty-message">No medications are expiring within the next 90 days.</p>
            <?php } ?>
        </div>

    </div>

</body>
</html>

<?php
$conn->close();
?>