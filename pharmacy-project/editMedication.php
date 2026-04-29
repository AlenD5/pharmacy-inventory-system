<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

if ($_SESSION['role'] != 'Admin' && $_SESSION['role'] != 'Pharmacist') {
    header("Location: dashboard.php");
    exit();
}

include "db_connect.php";

$message = "";
$error = "";

if (!isset($_GET['id'])) {
    header("Location: medications.php");
    exit();
}

$medication_id = (int) $_GET['id'];

/*
    Load suppliers for dropdown.
*/
$supplierSql = "SELECT supplier_id, supplier_name
                FROM suppliers
                ORDER BY supplier_name ASC";

$supplierResult = $conn->query($supplierSql);

/*
    Update medication after form submission.
    This updates only the medications table.
    Inventory quantity is updated through transactions.php.
*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medication_name = $_POST['medication_name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $reorder_threshold = (int) $_POST['reorder_threshold'];
    $supplier_id = (int) $_POST['supplier_id'];

    if (
        $medication_name == "" ||
        $category == "" ||
        $description == "" ||
        $reorder_threshold < 0 ||
        $supplier_id <= 0
    ) {
        $error = "Please fill out all fields correctly.";
    } else {
        $updateSql = "UPDATE medications
                      SET medication_name = ?,
                          category = ?,
                          description = ?,
                          reorder_threshold = ?,
                          supplier_id = ?
                      WHERE medication_id = ?";

        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param(
            "sssiii",
            $medication_name,
            $category,
            $description,
            $reorder_threshold,
            $supplier_id,
            $medication_id
        );

        if ($stmt->execute()) {
            $message = "Medication updated successfully.";
        } else {
            $error = "Error updating medication.";
        }

        $stmt->close();
    }
}

/*
    Load medication data for the form.
*/
$medSql = "SELECT *
           FROM medications
           WHERE medication_id = ?";

$medStmt = $conn->prepare($medSql);
$medStmt->bind_param("i", $medication_id);
$medStmt->execute();
$medResult = $medStmt->get_result();

if ($medResult->num_rows == 0) {
    header("Location: medications.php");
    exit();
}

$medication = $medResult->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Medication</title>

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
            width: 85%;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
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

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #aaa;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
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

        .back-link {
            display: inline-block;
            margin-top: 12px;
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

        <div class="role-note">
            Logged in as: <strong><?php echo $_SESSION['role']; ?></strong>
        </div>

        <div class="section-box">
            <h2 class="page-title">Edit Medication</h2>
            <p class="description">
                Update medication details. Stock quantities should be updated through the Transactions page.
            </p>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <form action="editMedication.php?id=<?php echo $medication_id; ?>" method="post">

                <div class="form-grid">

                    <div class="form-row">
                        <label for="medication_name">Medication Name</label>
                        <input type="text" id="medication_name" name="medication_name"
                               value="<?php echo $medication['medication_name']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category"
                               value="<?php echo $medication['category']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="reorder_threshold">Reorder Threshold</label>
                        <input type="number" id="reorder_threshold" name="reorder_threshold" min="0"
                               value="<?php echo $medication['reorder_threshold']; ?>" required>
                    </div>

                    <div class="form-row">
                        <label for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id" required>
                            <option value="">Select supplier...</option>

                            <?php while ($supplier = $supplierResult->fetch_assoc()) { ?>
                                <option value="<?php echo $supplier['supplier_id']; ?>"
                                    <?php if ($supplier['supplier_id'] == $medication['supplier_id']) { echo "selected"; } ?>>
                                    <?php echo $supplier['supplier_name']; ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                </div>

                <div class="form-row">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo $medication['description']; ?></textarea>
                </div>

                <button type="submit" class="btn">Save Changes</button>

            </form>

            <a href="medications.php" class="back-link">Back to Medications</a>
        </div>

    </div>

</body>
</html>

<?php
$medStmt->close();
$conn->close();
?>