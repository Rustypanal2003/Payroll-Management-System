<?php
session_start();
include 'dbconnect.php'; // Adjust path to your DB connection file

// Check if admin is logged in (adjust auth as needed)
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Get employee ID from GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: employee.php");
    exit();
}
$employee_id = (int)$_GET['id'];

// Fetch departments and positions for dropdowns
$departments = $conn->query("SELECT * FROM departments ORDER BY department_name");
$positions = $conn->query("SELECT * FROM positions ORDER BY position_name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $department_id = (int)$_POST['department_id'];
    $position_id = (int)$_POST['position_id'];
    $hire_date = $conn->real_escape_string($_POST['hire_date']);
    $salary_rate = $conn->real_escape_string($_POST['salary_rate']);

    if (!$firstname || !$lastname || !$department_id || !$position_id || !$hire_date || !$salary_rate) {
        $error = "Please fill in all required fields.";
    } else {
        // Update employee info including salary_rate
        $stmt = $conn->prepare("UPDATE employees SET firstname=?, lastname=?, department_id=?, position_id=?, salary_rate=?, hire_date=? WHERE employee_id=?");
        $stmt->bind_param("ssisdsi", $firstname, $lastname, $department_id, $position_id, $salary_rate, $hire_date, $employee_id);

        if ($stmt->execute()) {
            $success = "Employee updated successfully.";
        } else {
            $error = "Failed to update employee: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch current employee data
$stmt = $conn->prepare("SELECT firstname, lastname, department_id, position_id, salary_rate, hire_date FROM employees WHERE employee_id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: employee.php");
    exit();
}

$employee = $result->fetch_assoc();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Employee</title>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    form { max-width: 600px; background: #f9f9f9; padding: 20px; border-radius: 6px; }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input[type=text], input[type=date], input[type=number], select {
        width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;
    }
    button {
        margin-top: 15px; padding: 10px 15px; background: #4caf50; border: none; border-radius: 4px; color: white;
        cursor: pointer;
    }
    button:hover { background: #45a049; }
    .btn-cancel {
        background: #f44336;
        margin-left: 10px;
    }
    .btn-cancel:hover {
        background: #d32f2f;
    }
    .message { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
    .error { background: #f8d7da; color: #842029; }
    .success { background: #d1e7dd; color: #0f5132; }
</style>
</head>
<body>

<h2>Edit Employee ID <?= htmlspecialchars($employee_id) ?></h2>

<?php if ($error): ?>
  <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="message success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" action="">
    <label for="firstname">Firstname</label>
    <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($employee['firstname']) ?>" required />

    <label for="lastname">Lastname</label>
    <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($employee['lastname']) ?>" required />

    <label for="department_id">Department</label>
    <select id="department_id" name="department_id" required>
        <option value="">Select Department</option>
        <?php
        $departments->data_seek(0);
        while ($dept = $departments->fetch_assoc()):
        ?>
        <option value="<?= $dept['department_id'] ?>" <?= $dept['department_id'] == $employee['department_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($dept['department_name']) ?>
        </option>
        <?php endwhile; ?>
    </select>

    <label for="position_id">Position</label>
    <select id="position_id" name="position_id" required>
        <option value="">Select Position</option>
        <?php
        $positions->data_seek(0);
        while ($pos = $positions->fetch_assoc()):
        ?>
        <option value="<?= $pos['position_id'] ?>" <?= $pos['position_id'] == $employee['position_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($pos['position_name']) ?>
        </option>
        <?php endwhile; ?>
    </select>

    <label for="salary_rate">Salary Rate</label>
    <input type="number" step="0.01" id="salary_rate" name="salary_rate" value="<?= htmlspecialchars($employee['salary_rate']) ?>" required />

    <label for="hire_date">Hire Date</label>
    <input type="date" id="hire_date" name="hire_date" value="<?= htmlspecialchars($employee['hire_date']) ?>" required />

    <button type="submit">Update Employee</button>
    <button type="button" class="btn-cancel" onclick="window.location.href='employee.php'">Cancel</button>
</form>

</body>
</html>
