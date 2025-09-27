<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Shoe Creation Project</title>
    <style>
        /* Basic text styling */
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
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            font-size: 32px;
        }
        p {
            color: #333;
            font-size: 18px;
        }
        a {
            display: inline-block;
            margin: 10px;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 18px;
            transition: background 0.3s;
        }
        a:hover {
            background-color: #0056b3;
        }
        .logout-btn {
            background-color: #dc3545;
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
        .logout-btn:hover {
            background-color: #c82333;
        }
        .admin-btn {
            background-color: #ffc107;
            color: black;
            border: none;
            padding: 12px 24px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .admin-btn:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Homepage, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
        <h3>Thank you for logging in. You can now create and view your custom shoes.</h3>
        <p>Here are your options:</p>
        <a href="shoe_maker.php">Create a Custom Shoe</a>
        <a href="shoe_viewer.php">View My Shoes</a>
        <?php if ($_SESSION['user'] === 'dnib'): ?>
            <a href="admin.php" class="admin-btn">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>