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
    <title>Shoe Maker</title>
    <style>
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
            text-align: center;
        }
        h1 {
            color: #2c3e50;
            font-size: 32px;
        }
        .color-wheel {
            margin: 20px 0;
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
            transition: background 0.3s;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .shoe-type {
            margin: 20px 0;
        }
        .shoe-type label {
            margin-right: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Customize Your Shoe</h1>
        <p>Select a shoe type and customize its colors:</p>
        <form action="shoe_finisher.php" method="post">
            <!-- Shoe Type Selection -->
            <div class="shoe-type">
                <label>
                    <input type="radio" name="shoe_type" value="boot" required>
                    Boot
                </label>
                <label>
                    <input type="radio" name="shoe_type" value="dress-shoe" required>
                    Dress Shoe
                </label>
                <label>
                    <input type="radio" name="shoe_type" value="hightop" required>
                    High Top
                </label>
                <label>
                    <input type="radio" name="shoe_type" value="sport-sneaker" required>
                    Sport Sneaker
                </label>
                <label>
                    <input type="radio" name="shoe_type" value="casual-sneaker" required>
                    Casual Sneaker
                </label>
                <label>
                    <input type="radio" name="shoe_type" value="basketball-sneaker" required>
                    Basketball Sneaker
                </label>
            </div>

            <!-- Color Customization -->
            <label for="sole_color">Sole Color:</label><br>
            <input type="color" name="sole_color" class="color-wheel" value="#000000" required><br><br>

            <label for="upper_color">Upper Color:</label><br>
            <input type="color" name="upper_color" class="color-wheel" value="#000000" required><br><br>

            <label for="laces_color">Laces Color:</label><br>
            <input type="color" name="laces_color" class="color-wheel" value="#000000" required><br><br>

            <label for="shoe_name">Shoe Name:</label><br>
            <input type="text" id="shoe_name" name="shoe_name" placeholder="Enter a name for your shoe" required><br><br>

            <button type="submit" class="submit-btn">Finish Customization</button>
        </form>
    </div>
</body>
</html>