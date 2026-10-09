<?php
// index.php
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training Enrollment System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <a href="index.php">Home</a> |
    <a href="admin/courses.php">Courses</a> |
    <a href="admin/classes.php">Classes</a> |
    <a href="admin/students.php">Record Student</a> |
    <a href="admin/enroll.php">Enroll</a> |
    <a href="admin/enrollments.php">Enrollments</a>
</nav>
<main>
    <h1>Training Enrollment System</h1>
    <p>Welcome to the Multi-Table PHP PDO Training Enrollment Portal.</p>
    <ul>
        <li><a href="admin/courses.php">Manage Courses</a></li>
        <li><a href="admin/classes.php">Manage Classes</a></li>
        <li><a href="admin/students.php">Record Student (New Student + Class)</a></li>
        <li><a href="admin/enroll.php">Enroll Existing Student</a></li>
        <li><a href="admin/enrollments.php">View All Enrollments</a></li>
    </ul>
</main>
<footer>&copy; <?php echo date('Y'); ?> IPT Training Enrollment</footer>
</body>
</html>