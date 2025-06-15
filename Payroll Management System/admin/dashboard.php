<?php
$conn = new mysqli("localhost", "root", "", "payroll");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Count total employees, departments, and positions
$employees = $conn->query("SELECT COUNT(*) AS total FROM employees")->fetch_assoc()['total'];
$departments = $conn->query("SELECT COUNT(*) AS total FROM departments")->fetch_assoc()['total'];
$positions = $conn->query("SELECT COUNT(*) AS total FROM positions")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="employee.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="employee.php">Employees</a>
    <a href="manageuser.php">Manage User</a>
    <a href="">Reports</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Dashboard Overview</h2>

    <div class="dashboard-cards">
        <div class="card">
            <h3>Total Employees</h3>
            <p><?= $employees ?></p>
        </div>
        <div class="card">
            <h3>Total Departments</h3>
            <p><?= $departments ?></p>
        </div>
        <div class="card">
            <h3>Total Positions</h3>
            <p><?= $positions ?></p>
        </div>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
