<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Medication Tracking - Login</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #e8f4f8;
        }

        .login-container {
            width: 400px;
            margin: 100px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            color: #0b5c75;
            margin-bottom: 10px;
        }

        h2 {
            color: #333;
            margin-bottom: 25px;
        }

        label {
            display: block;
            text-align: left;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #aaa;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            background-color: #0b5c75;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #08475c;
        }

        .footer-text {
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h1>Pharmacy Tracker</h1>
        <h2>Medication Inventory Login</h2>

        <form action="login.php" method="post" autocomplete="off">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" autocomplete="off" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" autocomplete="new-password" required>

            <button type="submit">Login</button>
        </form>

        <p class="footer-text">Authorized pharmacy staff only</p>
    </div>

</body>
</html>