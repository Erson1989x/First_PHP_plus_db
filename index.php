<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
// Define the registerUser function
function registerUser($pdo, $username, $password, $email) {
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        return false; // User already exists
    }
    
    // Hash the password
    $pwdHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, pwd, email) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $pwdHash, $email]);
}

$host = 'localhost';
$dbname = 'myfirstdb';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
    // Test the connection by running a simple query
    $stmt = $pdo->query("SELECT 'Connection successful!' AS message");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p style='color: green; font-weight: bold;'>" . $result['message'] . "</p>";
    
    // Test if the users table exists and get count
    $stmt = $pdo->query("SELECT COUNT(*) AS user_count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<p>Number of users in database: " . $result['user_count'] . "</p>";
    
    // Test registering a user
    $result = registerUser($pdo, 'testuser', 'password123', 'test@example.com');
    if ($result) {
        echo "<p style='color: green;'>User registration successful!</p>";
    } else {
        echo "<p style='color: orange;'>User registration failed. User may already exist.</p>";
    }
    
} catch(PDOException $e) {
    die("<p style='color: red; font-weight: bold;'>Database error: " . $e->getMessage() . "</p>");


}

// Display all users
$stmt = $pdo->query("SELECT id, username, email, created_at FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>User List</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created At</th></tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . $user['username'] . "</td>";
    echo "<td>" . $user['email'] . "</td>";
    echo "<td>" . $user['created_at'] . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
    
</body>
</html>