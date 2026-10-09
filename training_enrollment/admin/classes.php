<?php
// admin/classes.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$courseObj = new Course($db);
$classObj  = new ClassSection($db);
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id  = (int)($_POST['course_id'] ?? 0);
    $class_code = trim($_POST['class_code'] ?? '');
    $schedule   = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $slots      = (int)($_POST['slots'] ?? 0);

    if ($course_id > 0 && !empty($class_code) && $slots >= 0) {
        if ($classObj->create($course_id, $class_code, $schedule, $instructor, $slots)) {
            $message = "Class section added successfully!";
        } else {
            $error = "Failed to add class section.";
        }
    } else {
        $error = "Please provide valid class information.";
    }
}

$courses = $courseObj->all();
$classes = $classObj->allWithCourse();
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Class Management</h2>

<?php if ($message): ?><div class="alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<form method="POST" action="classes.php">
    <h3>Add New Class Section</h3>
    <label>Select Course:</label>
    <select name="course_id" required>
        <option value="">-- Choose Course --</option>
        <?php foreach ($courses as $c): ?>
            <option value="<?php echo $c['course_id']; ?>">
                <?php echo htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Class Code:</label>
    <input type="text" name="class_code" required placeholder="e.g., SEC-A">

    <label>Schedule:</label>
    <input type="text" name="schedule" placeholder="e.g., Mon/Wed 9:00 AM - 11:00 AM">

    <label>Instructor:</label>
    <input type="text" name="instructor" placeholder="e.g., Prof. Smith">

    <label>Available Slots:</label>
    <input type="number" name="slots" min="0" value="10" required>

    <button type="submit">Add Class</button>
</form>

<h3>Class List</h3>
<table>
    <thead>
        <tr>
            <th>Class ID</th>
            <th>Course</th>
            <th>Class Code</th>
            <th>Schedule</th>
            <th>Instructor</th>
            <th>Slots Remaining</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($classes as $cl): ?>
            <tr>
                <td><?php echo htmlspecialchars($cl['class_id']); ?></td>
                <td><?php echo htmlspecialchars($cl['course_name']); ?></td>
                <td><?php echo htmlspecialchars($cl['class_code']); ?></td>
                <td><?php echo htmlspecialchars($cl['schedule']); ?></td>
                <td><?php echo htmlspecialchars($cl['instructor']); ?></td>
                <td><strong><?php echo htmlspecialchars($cl['slots']); ?></strong></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($classes)): ?>
            <tr><td colspan="6">No classes available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>