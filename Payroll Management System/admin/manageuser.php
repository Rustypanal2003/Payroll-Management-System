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

// Handle Add User
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // plain text for now
    $role = $conn->real_escape_string($_POST['role']);

    $check = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        $conn->query("INSERT INTO users (username, password, role, created_at) VALUES ('$username', '$password', '$role', NOW())");
        if ($conn->affected_rows > 0) {
            $success = "User added successfully.";
        } else {
            $error = "Failed to add user.";
        }
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id = $user_id");
    header("Location: manageuser.php");
    exit();
}



// Fetch users
$result = $conn->query("SELECT * FROM users ORDER BY user_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage User - Admin Panel</title>
    <link rel="stylesheet" href="manageuser.css" />
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="employee.php">Employees</a>
    <a href="manageuser.php" class="active">Manage User</a>
    <a href="#">Reports</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Manage User</h2>

    <?php if ($error): ?>
        <div style="color: red; font-weight: 600; margin-bottom: 10px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="color: green; font-weight: 600; margin-bottom: 10px;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <button class="btn btn-add" id="openAddModal">+ Add User</button>

    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                    <td>
                    <a href="edituser.php?id=<?= $user['user_id'] ?>" class="btn btn-edit">Edit</a>

                        <a href="?delete=<?= $user['user_id'] ?>" 
                            class="btn btn-delete" 
                            onclick="return confirm('Delete this user?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php if ($result->num_rows === 0): ?>
                <tr><td colspan="5" style="text-align:center; padding: 20px;">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal" id="addModal">
    <div class="modal-content">
        <h3>Add New User</h3>
        <form method="post" action="">
            <input type="hidden" name="action" value="add" />
            <label for="add-username">Username</label>
            <input type="text" id="add-username" name="username" required />
            <label for="add-password">Password</label>
            <input type="text" id="add-password" name="password" required />
            <label for="add-role">Role</label>
            <select id="add-role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button class="btn btn-add" type="submit">Add User</button>
            <button type="button" class="btn close-btn" id="closeAddModal">Cancel</button>
        </form>
    </div>
</div>


<script>
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');

    document.getElementById('openAddModal').onclick = () => addModal.style.display = 'flex';
    document.getElementById('closeAddModal').onclick = () => addModal.style.display = 'none';
    document.getElementById('closeEditModal').onclick = () => editModal.style.display = 'none';

    window.onclick = (e) => {
        if (e.target === addModal) addModal.style.display = 'none';
        if (e.target === editModal) editModal.style.display = 'none';
    };

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit-user_id').value = btn.dataset.id;
            document.getElementById('edit-username').value = btn.dataset.username;
            document.getElementById('edit-password').value = '';
            document.getElementById('edit-role').value = btn.dataset.role;

            editModal.style.display = 'flex';
        });
    });
</script>

</body>
</html>
