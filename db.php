<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mywebsite";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // Add new user
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->close();
        
    } elseif (isset($_POST['update'])) {
        // Update user
        $id = (int)$_POST['id'];
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        
        $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $email, $id);
        $stmt->execute();
        $stmt->close();
        
    } elseif (isset($_GET['delete'])) {
        // Delete user
        $id = (int)$_GET['delete'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY reg_date DESC");

// Check if editing a user
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM users WHERE id=$id");
    $edit_user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP MySQL CRUD</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin: 20px 0; padding: 20px; background: #f9f9f9; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
    </style>
</head>
<body>
    <h1>User Management</h1>
    
    <!-- Add/Edit Form -->
    <form method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" 
                   value="<?= $edit_user ? $edit_user['username'] : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?= $edit_user ? $edit_user['email'] : '' ?>" required>
        </div>
        <?php if ($edit_user): ?>
            <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
            <button type="submit" name="update">Update User</button>
            <a href="?" style="margin-left: 10px;">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add">Add User</button>
        <?php endif; ?>
    </form>
    
    <!-- Users Table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users->num_rows > 0): ?>
                <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= $row['reg_date'] ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>">Edit</a> | 
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

<?php $conn->close(); ?>