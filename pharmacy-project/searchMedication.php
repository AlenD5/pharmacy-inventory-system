<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

$searchTerm = "";

if (isset($_POST['searchTerm'])) {
    $searchTerm = trim($_POST['searchTerm']);
}

if ($searchTerm == "") {
    header("Location: medications.php");
    exit();
}

$jarFile = "mysql-connector-j-8.1.0.jar";

$command = 'cd java && java -cp ".;' . $jarFile . '" pharmacy_inventory_management_system.SearchMedicationRunner ' . escapeshellarg($searchTerm);

$output = shell_exec($command);
$lines = explode("\n", trim($output));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medication Search Results</title>

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

        .controls input {
            padding: 13px;
            font-size: 15px;
            border: 1px solid #aaa;
            border-radius: 6px;
            width: 260px;
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

        .empty-message {
            background-color: #fff8e6;
            border-left: 5px solid #f0ad4e;
            padding: 15px;
            color: #555;
            border-radius: 5px;
            margin-top: 20px;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #0b5c75;
            font-weight: bold;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
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
                <h2 class="page-title">Medication Search Results</h2>
                <p class="description">
                    Showing Java HashMap search results for: <strong><?php echo htmlspecialchars($searchTerm); ?></strong>
                </p>
            </div>

            <div class="controls">
                <form action="searchMedication.php" method="post" style="display:inline;">
                    <input type="text" name="searchTerm" placeholder="Search medication..." required>
                    <button type="submit" class="btn">Search</button>
                </form>

                <a class="btn" href="medications.php">View All</a>
            </div>
        </div>

        <div class="role-note">
            Logged in as: <strong><?php echo $_SESSION['role']; ?></strong>
        </div>

        <?php
        $hasResults = false;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line == "" || 
                $line == "NOT_FOUND" || 
                $line == "NO_SEARCH_TERM" || 
                $line == "DATABASE_CONNECTION_FAILED" || 
                $line == "SEARCH_ERROR" ||
                str_contains($line, "Connected to MySQL")) {
                continue;
            }

            $hasResults = true;
        }
        ?>

        <?php if ($hasResults) { ?>

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

                <?php foreach ($lines as $line) { ?>
                    <?php
                    $line = trim($line);

                    if ($line == "" || 
                        $line == "NOT_FOUND" || 
                        $line == "NO_SEARCH_TERM" || 
                        $line == "DATABASE_CONNECTION_FAILED" || 
                        $line == "SEARCH_ERROR" ||
                        str_contains($line, "Connected to MySQL")) {
                        continue;
                    }

                    $parts = explode("|", $line);

                    if (count($parts) < 6) {
                        continue;
                    }

                    $medication_id = $parts[0];
                    $medication_name = $parts[1];
                    $category = $parts[2];
                    $description = $parts[3];
                    $reorder_threshold = $parts[4];
                    $supplier_name = $parts[5];
                    ?>

                    <tr>
                        <td><?php echo htmlspecialchars($medication_id); ?></td>
                        <td><?php echo htmlspecialchars($medication_name); ?></td>
                        <td><?php echo htmlspecialchars($category); ?></td>
                        <td><?php echo htmlspecialchars($description); ?></td>
                        <td><?php echo htmlspecialchars($reorder_threshold); ?></td>
                        <td><?php echo htmlspecialchars($supplier_name); ?></td>

                        <?php if ($_SESSION['role'] == 'Pharmacist' || $_SESSION['role'] == 'Admin') { ?>
                            <td>
                                <a class="btn edit-btn" href="editMedication.php?id=<?php echo htmlspecialchars($medication_id); ?>">Edit</a>
                            </td>
                        <?php } ?>
                    </tr>

                <?php } ?>
            </table>

        <?php } else { ?>

            <div class="empty-message">
                No medications matched your search.
            </div>

        <?php } ?>

        <a class="back-link" href="medications.php">Back to Medications</a>

    </div>

</body>
</html>