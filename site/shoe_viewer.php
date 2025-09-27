<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user'];

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=localhost;port=8889;dbname=project", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the user's saved shoes
    $stmt = $pdo->prepare("SELECT * FROM user_shoes WHERE username = ?");
    $stmt->execute([$username]);
    $shoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the user's collections
    $collections_stmt = $pdo->prepare("SELECT * FROM collections WHERE username = ?");
    $collections_stmt->execute([$username]);
    $collections = $collections_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch shoes in each collection
    $collection_shoes = [];
    foreach ($collections as $collection) {
        $stmt = $pdo->prepare("SELECT user_shoes.* FROM collection_shoes 
                               JOIN user_shoes ON collection_shoes.shoe_id = user_shoes.id 
                               WHERE collection_shoes.collection_id = ?");
        $stmt->execute([$collection['id']]);
        $collection_shoes[$collection['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create_collection'])) {
        // Create a new collection
        $collection_name = htmlspecialchars($_POST['collection_name']);
        $stmt = $pdo->prepare("INSERT INTO collections (username, collection_name) VALUES (?, ?)");
        $stmt->execute([$username, $collection_name]);
        header("Location: shoe_viewer.php");
        exit;
    } elseif (isset($_POST['delete_collection'])) {
        // Delete a collection
        $collection_id = intval($_POST['collection_id']);
        $stmt = $pdo->prepare("DELETE FROM collections WHERE id = ?");
        $stmt->execute([$collection_id]);
        header("Location: shoe_viewer.php");
        exit;
    } elseif (isset($_POST['add_to_collection'])) {
        // Add a shoe to a collection
        $collection_id = intval($_POST['collection_id']);
        $shoe_id = intval($_POST['shoe_id']);
        $stmt = $pdo->prepare("INSERT INTO collection_shoes (collection_id, shoe_id) VALUES (?, ?)");
        $stmt->execute([$collection_id, $shoe_id]);
        header("Location: shoe_viewer.php");
        exit;
    } elseif (isset($_POST['remove_from_collection'])) {
        // Remove a shoe from a collection
        $collection_id = intval($_POST['collection_id']);
        $shoe_id = intval($_POST['shoe_id']);
        $stmt = $pdo->prepare("DELETE FROM collection_shoes WHERE collection_id = ? AND shoe_id = ?");
        $stmt->execute([$collection_id, $shoe_id]);
        header("Location: shoe_viewer.php");
        exit;
    } elseif (isset($_POST['delete_shoe'])) {
        // Delete a shoe
        $shoe_id = intval($_POST['shoe_id']);
        try {
            $stmt = $pdo->prepare("DELETE FROM user_shoes WHERE id = ? AND username = ?");
            $stmt->execute([$shoe_id, $username]);
            header("Location: shoe_viewer.php");
            exit;
        } catch (PDOException $e) {
            echo "Error deleting shoe: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shoes and Collections</title>
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
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            font-size: 32px;
        }
        .btn {
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
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 14px;
        }
        .btn-toggle {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-toggle:hover {
            background-color: #5a6268;
        }
        .form-inline {
            display: inline-block;
            margin: 10px;
        }
        .collection-content {
            display: block; /* Default to visible */
            margin-top: 10px;
        }
    </style>
    <script> /* Credit: https://www.w3schools.com/howto/howto_js_toggle_hide_show.asp */
        function toggleCollection(collectionId) {
            const content = document.getElementById(`collection-${collectionId}`);
            if (content.style.display === "none") {
                content.style.display = "block";
            } else {
                content.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>My Shoes</h1>
        <?php if (empty($shoes)): ?>
            <p>You haven't saved any shoes yet. <a href="shoe_maker.php" class="btn">Create a Shoe</a></p>
        <?php else: ?>
            <p>Select a shoe to view or manage:</p>
            <?php foreach ($shoes as $shoe): ?>
                <div class="shoe">
                    <form method="post" action="made_shoe.php" class="form-inline">
                        <input type="hidden" name="shoe_name" value="<?php echo htmlspecialchars($shoe['shoe_name']); ?>">
                        <input type="hidden" name="sole_color" value="<?php echo htmlspecialchars($shoe['sole_color']); ?>">
                        <input type="hidden" name="upper_color" value="<?php echo htmlspecialchars($shoe['upper_color']); ?>">
                        <input type="hidden" name="laces_color" value="<?php echo htmlspecialchars($shoe['laces_color']); ?>">
                        <input type="hidden" name="shoe_type" value="<?php echo htmlspecialchars($shoe['shoe_type']); ?>">
                        <button type="submit" class="btn"><?php echo htmlspecialchars($shoe['shoe_name']); ?></button>
                    </form>
                    <form method="post" class="form-inline">
                        <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                        <button type="submit" name="delete_shoe" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="container">
        <h1>My Collections</h1>
        <form method="post" class="form-inline">
            <input type="text" name="collection_name" placeholder="New Collection Name" required>
            <button type="submit" name="create_collection" class="btn">Create Collection</button>
        </form>
        <?php if (empty($collections)): ?>
            <p>You haven't created any collections yet.</p>
        <?php else: ?>
            <?php foreach ($collections as $collection): ?>
                <div class="collection">
                    <h2>
                        <?php echo htmlspecialchars($collection['collection_name']); ?>
                        <button type="button" class="btn btn-toggle" onclick="toggleCollection(<?php echo $collection['id']; ?>)">Toggle</button>
                    </h2>
                    <form method="post" class="form-inline">
                        <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
                        <button type="submit" name="delete_collection" class="btn btn-danger btn-small">Delete Collection</button>
                    </form>
                    <div id="collection-<?php echo $collection['id']; ?>" class="collection-content">
                        <h3>Shoes in this Collection:</h3>
                        <?php if (empty($collection_shoes[$collection['id']])): ?>
                            <p>No shoes in this collection.</p>
                        <?php else: ?>
                            <?php foreach ($collection_shoes[$collection['id']] as $shoe): ?>
                                <div class="shoe">
                                    <p><?php echo htmlspecialchars($shoe['shoe_name']); ?></p>
                                    <form method="post" class="form-inline">
                                        <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
                                        <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                                        <button type="submit" name="remove_from_collection" class="btn btn-danger btn-small">Remove</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <h3>Add Shoes to this Collection:</h3>
                        <?php foreach ($shoes as $shoe): ?>
                            <form method="post" class="form-inline">
                                <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
                                <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                                <button type="submit" name="add_to_collection" class="btn btn-small"><?php echo htmlspecialchars($shoe['shoe_name']); ?></button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <a href="home.php" class="btn">Return to Homepage</a>
</body>
</html>