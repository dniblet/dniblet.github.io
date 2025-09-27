<?php
class AuthPDO {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=localhost;port=8889;dbname=project", // Corrected port
                "root",
                "root"
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function register($username, $password, $confirm) {
        if (empty($username) || empty($password) || empty($confirm)) {
            return "All fields are required.";
        }
        if ($password !== $confirm) {
            return "Passwords do not match.";
        }

        try {
            $hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $stmt = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                return "Username already exists.";
            }
            return "Error: " . $e->getMessage();
        }
    }

    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && password_verify($password, $user["password"]);
    }
}
?>
