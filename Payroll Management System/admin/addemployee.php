<?php
session_start();
$conn = new mysqli("localhost", "root", "", "payroll");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Simple auth check for admin (adjust as needed)
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Fetch departments and positions for dropdowns
$departments = $conn->query("SELECT * FROM departments ORDER BY department_name");
$positions = $conn->query("SELECT * FROM positions ORDER BY position_name");

// Handle Add Employee
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $department_id = (int)$_POST['department_id'];
    $position_id = (int)$_POST['position_id'];
    $hire_date = $conn->real_escape_string($_POST['hire_date']);

    // Simple validation
    if (!$firstname || !$lastname || !$department_id || !$position_id || !$hire_date) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO employees (firstname, lastname, department_id, position_id, hire_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiis", $firstname, $lastname, $department_id, $position_id, $hire_date);

        if ($stmt->execute()) {
            $success = "Employee added successfully.";
        } else {
            $error = "Failed to add employee.";
        }
        $stmt->close();
    }
}

// Handle Delete Employee
if (isset($_GET['delete'])) {
    $employee_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM employees WHERE employee_id = $employee_id");
    header("Location: manageemployee.php");
    exit();
}

// Fetch employees with join for department, position, and salary
$sql = "SELECT e.employee_id, e.firstname, e.lastname, d.department_name, p.position_name, p.salary_rate, e.hire_date
        FROM employees e
        JOIN departments d ON e.department_id = d.department_id
        JOIN positions p ON e.position_id = p.position_id
        ORDER BY e.employee_id DESC";

$employees = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Employees - Admin Panel</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h2 { margin-bottom: 20px; }
    form { margin-bottom: 30px; background: #f9f9f9; padding: 15px; border-radius: 6px; width: 600px; }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input[type=text], input[type=date], select {
        width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;
    }
    button {
        background-color: #4caf50; color: white; border: none; padding: 10px 15px; border-radius: 4px;
        cursor: pointer; margin-top: 15px;
    }
    button:hover {
        background-color: #45a049;
    }
    table {
        width: 100%; border-collapse: collapse;
    }
    th, td {
        padding: 10px; border-bottom: 1px solid #ddd; text-align: left;
    }
    th {
        background-color: #4caf50; color: white;
    }
    tr:hover {
        background-color: #f5f5f5;
    }
    .btn-delete {
        background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .btn-delete:hover {
        background-color: #d32f2f;
    }
    .message {
        padding: 10px; margin-bottom: 20px; border-radius: 5px;
    }
    .error {
        background-color: #f8d7da; color: #842029;
    }
    .success {
        background-color: #d1e7dd; color: #0f5132;
    }
</style>
<script>
  // Optional: Show salary rate automatically when position changes (optional enhancement)
  function updateSalary() {
    const positions = <?php 
        $posArray = [];
        foreach ($positions as $pos) {
          $posArray[$pos['position_id']] = $pos['salary_rate'];
        }
        echo json_encode($posArray);
      ?>;
    const positionSelect = document.getElementById('position_id');
    const salaryField = document.getElementById('salary_rate');
    const posId = positionSelect.value;
    salaryField.value = positions[posId] !== undefined ? positions[posId] : '';
  }
  window.onload = () => updateSalary();
</script>
</head>
<body>

<h2>Manage Employees</h2>

<?php if ($error): ?>
  <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="message success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" action="">
  <input type="hidden" name="action" value="add" />
  <label for="firstname">Firstname</label>
  <input type="text" id="firstname" name="firstname" required />

  <label for="lastname">Lastname</label>
  <input type="text" id="lastname" name="lastname" required />

  <label for="department_id">Department</label>
  <select id="department_id" name="department_id" required>
    <option value="">Select Department</option>
    <?php while ($dept = $departments->fetch_assoc()): ?>
      <option value="<?= $dept['department_id'] ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
    <?php endwhile; ?>
  </select>

  <label for="position_id">Position</label>
  <select id="position_id" name="position_id" required onchange="updateSalary()">
    <option value="">Select Position</option>
    <?php
      // Reset positions result pointer to beginning
      $positions->data_seek(0);
      while ($pos = $positions->fetch_assoc()):
    ?>
      <option value="<?= $pos['position_id'] ?>"><?= htmlspecialchars($pos['position_name']) ?></option>
    <?php endwhile; ?>
  </select>

  <label for="salary_rate">Salary Rate</label>
  <input type="text" id="salary_rate" name="salary_rate" disabled />

  <label for="hire_date">Hire Date</label>
  <input type="date" id="hire_date" name="hire_date" required />

  <button type="submit">Add Employee</button>
  <button type="button" onclick="window.location.href='employee.php'" style="background-color: #f44336; margin-left: 10px;">Cancel</button>
</form>

</form>

