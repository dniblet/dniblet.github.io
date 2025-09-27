<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'dnib') {
    header("Location: login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;port=8889;dbname=project", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all users and their shoes
    $users_stmt = $pdo->query("SELECT * FROM users");
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

    $shoes_stmt = $pdo->query("SELECT * FROM user_shoes");
    $shoes = $shoes_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all collections
    $collections_stmt = $pdo->query("SELECT * FROM collections");
    $collections = $collections_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch shoes in collections
    $collection_shoes_stmt = $pdo->query("
        SELECT collection_shoes.collection_id, user_shoes.id AS shoe_id, user_shoes.shoe_name 
        FROM collection_shoes
        JOIN user_shoes ON collection_shoes.shoe_id = user_shoes.id
    ");
    $collection_shoes_raw = $collection_shoes_stmt->fetchAll(PDO::FETCH_ASSOC);

    $collection_shoes = [];
    foreach ($collection_shoes_raw as $entry) {
        $collection_shoes[$entry['collection_id']][] = [
            'id' => $entry['shoe_id'],
            'shoe_name' => $entry['shoe_name']
        ];
    }
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Handle admin actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_shoe'])) {
        // Delete a shoe
        $shoe_id = intval($_POST['shoe_id']);
        $stmt = $pdo->prepare("DELETE FROM user_shoes WHERE id = ?");
        $stmt->execute([$shoe_id]);
    } elseif (isset($_POST['rename_shoe'])) {
        // Rename a shoe
        $shoe_id = intval($_POST['shoe_id']);
        $new_name = htmlspecialchars($_POST['new_name']);
        $stmt = $pdo->prepare("UPDATE user_shoes SET shoe_name = ? WHERE id = ?");
        $stmt->execute([$new_name, $shoe_id]);
    } elseif (isset($_POST['delete_user'])) {
        // Delete a user and their shoes
        $user_id = intval($_POST['user_id']);
        $stmt = $pdo->prepare("DELETE FROM user_shoes WHERE username = (SELECT username FROM users WHERE id = ?)");
        $stmt->execute([$user_id]);
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    } elseif (isset($_POST['delete_collection'])) {
        // Delete a collection
        $collection_id = intval($_POST['collection_id']);
        $stmt = $pdo->prepare("DELETE FROM collections WHERE id = ?");
        $stmt->execute([$collection_id]);
    } elseif (isset($_POST['rename_collection'])) {
        // Rename a collection
        $collection_id = intval($_POST['collection_id']);
        $new_name = htmlspecialchars($_POST['new_name']);
        $stmt = $pdo->prepare("UPDATE collections SET collection_name = ? WHERE id = ?");
        $stmt->execute([$new_name, $collection_id]);
    } elseif (isset($_POST['remove_from_collection'])) {
        // Remove a shoe from a collection
        $collection_id = intval($_POST['collection_id']);
        $shoe_id = intval($_POST['shoe_id']);
        $stmt = $pdo->prepare("DELETE FROM collection_shoes WHERE collection_id = ? AND shoe_id = ?");
        $stmt->execute([$collection_id, $shoe_id]);
    }
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
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
        .home-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 20px;
            display: inline-block;
        }
        .home-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <h2>Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger">Delete User</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Shoes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Shoe Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shoes as $shoe): ?>
                    <tr>
                        <td><?php echo $shoe['id']; ?></td>
                        <td><?php echo htmlspecialchars($shoe['username']); ?></td>
                        <td><?php echo htmlspecialchars($shoe['shoe_name']); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                                <input type="text" name="new_name" placeholder="New Name" required>
                                <button type="submit" name="rename_shoe" class="btn">Rename</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                                <button type="submit" name="delete_shoe" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Collections</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Collection Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($collections as $collection): ?>
                    <tr>
                        <td><?php echo $collection['id']; ?></td>
                        <td><?php echo htmlspecialchars($collection['username']); ?></td>
                        <td><?php echo htmlspecialchars($collection['collection_name']); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
                                <input type="text" name="new_name" placeholder="New Name" required>
                                <button type="submit" name="rename_collection" class="btn">Rename</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
                                <button type="submit" name="delete_collection" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <h4>Shoes in this Collection:</h4>
                            <?php if (empty($collection_shoes[$collection['id']])): ?>
                                <p>No shoes in this collection.</p>
                            <?php else: ?>
                                <ul>
                                    <?php foreach ($collection_shoes[$collection['id']] as $shoe): ?>
                                        <li>
                                            <?php echo htmlspecialchars($shoe['shoe_name']); ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
                                                <input type="hidden" name="shoe_id" value="<?php echo $shoe['id']; ?>">
                                                <button type="submit" name="remove_from_collection" class="btn btn-danger btn-small">Remove</button>
                                            </form>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="home.php" class="home-btn">Return Home</a>
    </div>
</body>
</html>