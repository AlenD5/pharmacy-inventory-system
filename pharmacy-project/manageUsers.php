<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: firstPage.php");
    exit();
}

if ($_SESSION['role'] != 'Admin') {
    header("Location: dashboard.php");
    exit();
}

include "db_connect.php";

$message = "";
$error = "";

/*
    DELETE USER LOGIC

    Admin can delete users, but deletion happens through POST,
    not through the URL. Admin also cannot delete their own account.
*/
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];

    if ($delete_id == $_SESSION['user_id']) {
        $error = "You cannot delete your own account while logged in.";
    } else {
        $deleteSql = "DELETE FROM users WHERE user_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $delete_id);

        if ($deleteStmt->execute()) {
            $message = "User deleted successfully.";
        } else {
            $error = "Error deleting user.";
        }

        $deleteStmt->close();
    }
}

/*
    CREATE USER LOGIC
*/
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_id'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($first_name == "" || $last_name == "" || $email == "" || $password == "" || $role == "") {
        $error = "All fields are required.";
    } else {
        $sql = "INSERT INTO users (first_name, last_name, email, password, role)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $role);

        if ($stmt->execute()) {
            $message = "User created successfully.";
        } else {
            $error = "Error creating user. The email may already exist.";
        }

        $stmt->close();
    }
}

/*
    DISPLAY CURRENT USERS
*/
$userSql = "SELECT user_id, first_name, last_name, email, role, created_at
            FROM users
            ORDER BY user_id ASC";

$userResult = $conn->query($userSql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>

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
        select {
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

        .delete-btn {
            background-color: #d9534f;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #b52b27;
        }

        .current-user-label {
            color: #555;
            font-weight: bold;
            font-size: 14px;
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
            <h2 class="page-title">Create New User</h2>
            <p class="description">
                Admin users can create accounts and assign access roles for the pharmacy system.
            </p>

            <?php if ($message != "") { ?>
                <div class="message"><?php echo $message; ?></div>
            <?php } ?>

            <?php if ($error != "") { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

            <form action="manageUsers.php" method="post">

                <div class="form-grid">
                    <div class="form-row">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>

                    <div class="form-row">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>

                    <div class="form-row">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-row">
                        <label for="password">Password</label>
                        <input type="text" id="password" name="password" required>
                    </div>

                    <div class="form-row">
                        <label for="role">Role</label>
                        <select id="role" name="role" required>
                            <option value="">Select role...</option>
                            <option value="Admin">Admin</option>
                            <option value="Pharmacist">Pharmacist</option>
                            <option value="Inventory Manager">Inventory Manager</option>
                            <option value="Technician">Technician</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn">Create User</button>

            </form>
        </div>

        <div class="section-box">
            <h2 class="page-title">Current Users</h2>
            <p class="description">
                Existing users and their assigned roles are listed below.
            </p>

            <table>
                <tr>
                    <th>User ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>

                <?php while ($row = $userResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['first_name']; ?></td>
                        <td><?php echo $row['last_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>

                        <td>
                            <?php if ($row['user_id'] != $_SESSION['user_id']) { ?>
                                <form action="manageUsers.php" method="post" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" class="delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this user?');">
                                        Delete
                                    </button>
                                </form>
                            <?php } else { ?>
                                <span class="current-user-label">Current User</span>
                            <?php } ?>
                        </td>
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