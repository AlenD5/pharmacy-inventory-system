<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

include "db_connect.php";


$sql = "SELECT 
            i.inventory_id,
            m.medication_name,
            m.category,
            i.quantity,
            m.reorder_threshold,
            i.expiration_date,
            i.location,
            i.last_updated
        FROM inventory i
        JOIN medications m 
            ON i.medication_id = m.medication_id
        ORDER BY m.medication_name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>

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
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.15);
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .page-title {
            color: #0b5c75;
            margin-bottom: 5px;
        }

        .description {
            color: #555;
            margin-top: 0;
        }

        .controls input,
        .controls select {
            padding: 13px;
            font-size: 15px;
            border: 1px solid #aaa;
            border-radius: 6px;
        }

        .controls input {
            width: 260px;
        }

        .controls select {
            width: 190px;
        }

        .btn {
            background-color: #0b5c75;
            color: white;
            padding: 10px 14px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #08475c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
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

        .role-note {
            margin-top: 15px;
            background-color: #e8f4f8;
            padding: 12px;
            border-left: 5px solid #0b5c75;
            color: #444;
        }

        .quantity-low {
            color: #d98c00;
            font-weight: bold;
        }

        .quantity-normal {
            color: green;
            font-weight: bold;
        }

        .low-label {
            display: inline-block;
            margin-left: 6px;
            padding: 3px 7px;
            background-color: #fff3cd;
            color: #8a5a00;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .ok-label {
            display: inline-block;
            padding: 3px 7px;
            background-color: #e6f4ea;
            color: green;
            border-radius: 10px;
            font-size: 12px;
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

        <div class="top-section">
			<div>
				<h2 class="page-title">Inventory Records</h2>
				<p class="description">
					View current stock quantities, reorder thresholds, expiration dates, storage locations, and last updated times.
				</p>
			</div>

			<?php if ($_SESSION['role'] == 'Inventory Manager' || $_SESSION['role'] == 'Admin') { ?>
				<a class="btn" href="transactions.php">Update Stock</a>
			<?php } ?>
        </div>

        <div class="role-note">
            Logged in as: <strong><?php echo $_SESSION['role']; ?></strong>
        </div>

        <table>
            <tr>
                <th>Inventory ID</th>
                <th>Medication Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Reorder Threshold</th>
                <th>Stock Status</th>
                <th>Expiration Date</th>
                <th>Location</th>
                <th>Last Updated</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['inventory_id']; ?></td>
                    <td><?php echo $row['medication_name']; ?></td>
                    <td><?php echo $row['category']; ?></td>

                    <td>
                        <?php if ($row['quantity'] < $row['reorder_threshold']) { ?>
                            <span class="quantity-low"><?php echo $row['quantity']; ?></span>
                        <?php } else { ?>
                            <span class="quantity-normal"><?php echo $row['quantity']; ?></span>
                        <?php } ?>
                    </td>

                    <td><?php echo $row['reorder_threshold']; ?></td>

                    <td>
                        <?php if ($row['quantity'] < $row['reorder_threshold']) { ?>
                            <span class="low-label">Low Stock</span>
                        <?php } else { ?>
                            <span class="ok-label">In Stock</span>
                        <?php } ?>
                    </td>

                    <td><?php echo $row['expiration_date']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><?php echo $row['last_updated']; ?></td>
                </tr>
            <?php } ?>
        </table>

    </div>

</body>
</html>

<?php
$conn->close();
?>