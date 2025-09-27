<?php
require_once './models/AuthPDO.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validate username
    if (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $username)) {
        $error = "Username must be 3-20 alphanumeric characters.";
    }
    // Check if passwords match
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $auth = new AuthPDO();
        $result = $auth->register($username, $password, $confirm);
        if ($result === true) {
            header("Location: login.php");
            exit;
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        /* Reuse styles from login.html */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 20px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 50%;
            text-align: center;
        }
        h1 {
            color: #2c3e50;
            font-size: 32px;
        }
        input[type="text"], input[type="password"] {
            margin-top: 5px;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 100%;
            max-width: 300px;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form action="signup.php" method="post">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br>
            <label for="confirm">Confirm Password:</label><br>
            <input type="password" id="confirm" name="confirm" required><br>
            <input type="submit" value="Sign Up" class="submit-btn">
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>