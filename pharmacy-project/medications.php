<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

include "db_connect.php";

$sql = "SELECT 
            m.medication_id,
            m.medication_name,
            m.category,
            m.description,
            m.reorder_threshold,
            s.supplier_name
        FROM medications m
        LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id
        ORDER BY m.medication_name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medications</title>

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

        .edit-btn {
            background-color: #f0ad4e;
        }

        .edit-btn:hover {
            background-color: #d99632;
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
                <h2 class="page-title">Medication Records</h2>
                <p class="description">
                    View medication details, categories, suppliers, and reorder thresholds.
                </p>
            </div>

			<div class="controls">

				<form action="searchMedication.php" method="post" style="display:inline;">
					<input type="text" name="searchTerm" placeholder="Search medication..." required>
					<button type="submit" class="btn">Search</button>
				</form>

				<form action="sortMedication.php" method="post" style="display:inline;">
					<button type="submit" class="btn">Sort by Expiration</button>
				</form>

				<?php if ($_SESSION['role'] == 'Pharmacist' || $_SESSION['role'] == 'Admin') { ?>
					<a class="btn" href="addMedication.php">Add Medication</a>
				<?php } ?>

			</div>
        </div>

        <div class="role-note">
            Logged in as: <strong><?php echo $_SESSION['role']; ?></strong>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Medication Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Reorder Threshold</th>
                <th>Supplier</th>

                <?php if ($_SESSION['role'] == 'Pharmacist' || $_SESSION['role'] == 'Admin') { ?>
                    <th>Actions</th>
                <?php } ?>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['medication_id']; ?></td>
                    <td><?php echo $row['medication_name']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['reorder_threshold']; ?></td>
                    <td><?php echo $row['supplier_name']; ?></td>

                    <?php if ($_SESSION['role'] == 'Pharmacist' || $_SESSION['role'] == 'Admin') { ?>
                        <td>
                            <a class="btn edit-btn" href="editMedication.php?id=<?php echo $row['medication_id']; ?>">Edit</a>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>

    </div>

</body>
</html>

<?php
$conn->close();
?>