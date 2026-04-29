<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

include "db_connect.php";

/*
    Only Manager, Admin, and Pharmacist can see the report page
*/
if (
    $_SESSION['role'] != 'Admin' &&
    $_SESSION['role'] != 'Pharmacist' &&
    $_SESSION['role'] != 'Inventory Manager'
) {
    header("Location: dashboard.php");
    exit();
}

/*
    REPORT 1: Most Used Medications
*/
$mostUsedSql = "SELECT 
                    m.medication_name,
                    COUNT(t.transaction_id) AS times_used,
                    SUM(ABS(t.amount_changed)) AS total_quantity_used
                FROM transactions t
                JOIN inventory i 
                    ON t.inventory_id = i.inventory_id
                JOIN medications m 
                    ON i.medication_id = m.medication_id
                WHERE t.transaction_type = 'Stock Out'
                GROUP BY m.medication_name
                ORDER BY total_quantity_used DESC";

$mostUsedResult = $conn->query($mostUsedSql);


/*
    REPORT 2: Monthly Usage Trends
*/
$monthlyUsageSql = "SELECT 
                        m.medication_name,
                        DATE_FORMAT(t.transaction_date, '%Y-%m') AS month,
                        SUM(ABS(t.amount_changed)) AS total_usage
                    FROM transactions t
                    JOIN inventory i 
                        ON t.inventory_id = i.inventory_id
                    JOIN medications m 
                        ON i.medication_id = m.medication_id
                    WHERE t.transaction_type = 'Stock Out'
                    GROUP BY m.medication_name, month
                    ORDER BY m.medication_name, month";

$monthlyUsageResult = $conn->query($monthlyUsageSql);


/*
    REPORT 3: Medications Below Reorder Threshold
*/
$belowThresholdSql = "SELECT 
                        m.medication_name,
                        i.quantity,
                        m.reorder_threshold,
                        (m.reorder_threshold - i.quantity) AS units_below_threshold
                    FROM medications m
                    JOIN inventory i
                        ON m.medication_id = i.medication_id
                    WHERE i.quantity < m.reorder_threshold
                    ORDER BY units_below_threshold DESC";

$belowThresholdResult = $conn->query($belowThresholdSql);


/*
    REPORT 4: High-Risk Medications
*/
$highRiskSql = "SELECT
                    m.medication_name,
                    i.quantity AS current_quantity,
                    m.reorder_threshold,
                    (m.reorder_threshold - i.quantity) AS units_below_threshold,
                    COUNT(t.transaction_id) AS times_reduced,
                    COALESCE(SUM(ABS(t.amount_changed)), 0) AS total_quantity_removed
                FROM medications m
                JOIN inventory i
                    ON m.medication_id = i.medication_id
                LEFT JOIN transactions t
                    ON i.inventory_id = t.inventory_id
                    AND t.transaction_type = 'Stock Out'
                GROUP BY
                    m.medication_name,
                    i.quantity,
                    m.reorder_threshold
                HAVING i.quantity < m.reorder_threshold
                ORDER BY
                    times_reduced DESC,
                    units_below_threshold DESC";

$highRiskResult = $conn->query($highRiskSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>

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

        .risk-high {
            color: #d9534f;
            font-weight: bold;
        }

        .risk-warning {
            color: #d98c00;
            font-weight: bold;
        }

        .empty-message {
            background-color: #e6f4ea;
            color: green;
            padding: 15px;
            border-radius: 6px;
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
            <h2 class="page-title">Most Used Medications</h2>
            <p class="description">
                This report shows which medications have been removed from inventory the most.
            </p>

            <?php if ($mostUsedResult->num_rows > 0) { ?>
                <table>
                    <tr>
                        <th>Medication Name</th>
                        <th>Times Used</th>
                        <th>Total Quantity Used</th>
                    </tr>

                    <?php while ($row = $mostUsedResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['medication_name']; ?></td>
                            <td><?php echo $row['times_used']; ?></td>
                            <td><?php echo $row['total_quantity_used']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p class="empty-message">No medication usage data found.</p>
            <?php } ?>
        </div>

        <div class="section-box">
            <h2 class="page-title">Monthly Usage Trends</h2>
            <p class="description">
                This report shows medication usage by month based on Stock Out transactions.
            </p>

            <?php if ($monthlyUsageResult->num_rows > 0) { ?>
                <table>
                    <tr>
                        <th>Medication Name</th>
                        <th>Month</th>
                        <th>Total Usage</th>
                    </tr>

                    <?php while ($row = $monthlyUsageResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['medication_name']; ?></td>
                            <td><?php echo $row['month']; ?></td>
                            <td><?php echo $row['total_usage']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p class="empty-message">No monthly usage data found.</p>
            <?php } ?>
        </div>

        <div class="section-box">
            <h2 class="page-title">Medications Below Reorder Threshold</h2>
            <p class="description">
                This report identifies medications where current quantity is below the reorder threshold.
            </p>

            <?php if ($belowThresholdResult->num_rows > 0) { ?>
                <table>
                    <tr>
                        <th>Medication Name</th>
                        <th>Current Quantity</th>
                        <th>Reorder Threshold</th>
                        <th>Units Below Threshold</th>
                    </tr>

                    <?php while ($row = $belowThresholdResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['medication_name']; ?></td>
                            <td class="risk-warning"><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['reorder_threshold']; ?></td>
                            <td class="risk-warning"><?php echo $row['units_below_threshold']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p class="empty-message">No medications are below reorder threshold.</p>
            <?php } ?>
        </div>

        <div class="section-box">
            <h2 class="page-title">High-Risk Medications</h2>
            <p class="description">
                This report combines low stock and high usage to identify medications at higher stockout risk.
            </p>

            <?php if ($highRiskResult->num_rows > 0) { ?>
                <table>
                    <tr>
                        <th>Medication Name</th>
                        <th>Current Quantity</th>
                        <th>Reorder Threshold</th>
                        <th>Units Below Threshold</th>
                        <th>Times Reduced</th>
                        <th>Total Quantity Removed</th>
                    </tr>

                    <?php while ($row = $highRiskResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['medication_name']; ?></td>
                            <td class="risk-high"><?php echo $row['current_quantity']; ?></td>
                            <td><?php echo $row['reorder_threshold']; ?></td>
                            <td class="risk-high"><?php echo $row['units_below_threshold']; ?></td>
                            <td><?php echo $row['times_reduced']; ?></td>
                            <td><?php echo $row['total_quantity_removed']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p class="empty-message">No high-risk medications found.</p>
            <?php } ?>
        </div>

    </div>

</body>
</html>

<?php
$conn->close();
?>