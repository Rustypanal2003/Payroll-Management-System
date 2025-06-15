<?php
$conn = new mysqli("localhost", "root", "", "payroll");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            e.employee_id, 
            e.firstname, 
            e.lastname, 
            d.department_name, 
            p.position_name, 
            p.salary_rate, 
            e.hire_date
        FROM employees e
        JOIN departments d ON e.department_id = d.department_id
        JOIN positions p ON e.position_id = p.position_id
        ORDER BY e.employee_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Payroll Management - Employees List</title>
    <link rel="stylesheet" href="employee.css">
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="employee.php" class="active">Employees</a>
    <a href="manageuser.php">Manage User</a>
    <a href="">Reports</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Employees List</h2>

    <div class="actions">
        <a href="addemployee.php" class="btn-add">+ Add Employee</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Firstname</th>
                <th>Lastname</th>
                <th>Department</th>
                <th>Position</th>
                <th>Salary Rate</th>
                <th>Hire Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['employee_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['department_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['position_name']) . "</td>";
                    echo "<td>₱ " . number_format($row['salary_rate'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['hire_date']) . "</td>";
                    echo "<td>
                        <a href='edit.php?id={$row['employee_id']}' class='btn-edit'>Edit</a>
                        <a href='delete.php?id={$row['employee_id']}' class='btn-delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' style='text-align:center;'>No employees found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
