<?php
// admin/courses.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';

$courseObj = new Course($db);
$message = '';
$error = '';

// Handle Form Submission (Create Course)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['course_code'] ?? '');
    $name = trim($_POST['course_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if (!empty($code) && !empty($name)) {
        if ($courseObj->create($code, $name, $desc)) {
            $message = "Course added successfully!";
        } else {
            $error = "Failed to add course. Course Code must be unique.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

$courses = $courseObj->all();
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Course Management</h2>

<?php if ($message): ?>
    <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="courses.php">
    <h3>Add New Course</h3>
    <label>Course Code:</label>
    <input type="text" name="course_code" required placeholder="e.g., CS101">

    <label>Course Name:</label>
    <input type="text" name="course_name" required placeholder="e.g., Web Development Fundamentals">

    <label>Description:</label>
    <textarea name="description" rows="3"></textarea>

    <button type="submit">Add Course</button>
</form>

<h3>Course List</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courses as $c): ?>
            <tr>
                <td><?php echo htmlspecialchars($c['course_id']); ?></td>
                <td><?php echo htmlspecialchars($c['course_code']); ?></td>
                <td><?php echo htmlspecialchars($c['course_name']); ?></td>
                <td><?php echo htmlspecialchars($c['description']); ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($courses)): ?>
            <tr><td colspan="4">No courses available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>