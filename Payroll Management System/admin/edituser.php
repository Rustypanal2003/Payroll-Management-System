<?php
session_start();
$conn = new mysqli("localhost", "root", "", "payroll");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header("Location: manageuser.php");
    exit();
}

$user_id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // plain text for now
    $role = $conn->real_escape_string($_POST['role']);

    // Check if username is taken by other users
    $check = $conn->query("SELECT * FROM users WHERE username = '$username' AND user_id != $user_id");
    if ($check->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        $sql = "UPDATE users SET username='$username', role='$role'";
        if (!empty($password)) {
            $sql .= ", password='$password'";
        }
        $sql .= " WHERE user_id=$user_id";

        if ($conn->query($sql)) {
            $success = "User updated successfully.";
            header("Location: manageuser.php?success=" . urlencode($success));
            exit();
        } else {
            $error = "Failed to update user.";
        }
    }
}

// Fetch user info for form
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
if ($result->num_rows === 0) {
    header("Location: manageuser.php");
    exit();
}
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit User - Admin Panel</title>
    <link rel="stylesheet" href="edituser.css" />
</head>
<body>



<div class="main-content">
    <h2>Edit User</h2>

    <?php if ($error): ?>
        <div style="color: red; font-weight: 600; margin-bottom: 10px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required value="<?= htmlspecialchars($user['username']) ?>" />

        <label for="password">Password (leave blank to keep unchanged)</label>
        <input type="text" id="password" name="password" value="" />

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>

        <button class="btn btn-edit" type="submit">Save Changes</button>
        <a href="manageuser.php" class="btn btn-cancel" style="margin-left: 10px;">Cancel</a>
    </form>
</div>

</body>
</html>
