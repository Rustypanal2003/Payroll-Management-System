<?php
include 'dbconnect.php';  // Import DB connection

if (!isset($_GET['id'])) {
    header("Location: employee.php");
    exit();
}

$employee_id = (int)$_GET['id'];

if ($employee_id <= 0) {
    // Invalid ID
    header("Location: employee.php?msg=" . urlencode("Invalid employee ID."));
    exit();
}

// Prepare and execute delete statement
$stmt = $conn->prepare("DELETE FROM employees WHERE employee_id = ?");
$stmt->bind_param("i", $employee_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        header("Location: employee.php?msg=" . urlencode("Employee deleted successfully."));
        exit();
    } else {
        $stmt->close();
        header("Location: employee.php?msg=" . urlencode("Employee not found or already deleted."));
        exit();
    }
} else {
    $stmt->close();
    header("Location: employee.php?msg=" . urlencode("Failed to delete employee."));
    exit();
}
