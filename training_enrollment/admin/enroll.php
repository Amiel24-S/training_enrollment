<?php
// admin/enroll.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../classes/Student.php';

$repo = new EnrollmentRepository($db);
$classObj = new ClassSection($db);
$studentObj = new Student($db);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $class_id   = (int)($_POST['class_id'] ?? 0);

    if ($student_id > 0 && $class_id > 0) {
        if ($repo->enroll($student_id, $class_id)) {
            $message = "Existing student successfully enrolled!";
        } else {
            $error = "Enrollment failed: No available slots for this class.";
        }
    } else {
        $error = "Please select both a student and a class.";
    }
}

$students = $studentObj->all();
$classList = $classObj->allWithCourse();
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Enroll Existing Student</h2>

<?php if ($message): ?><div class="alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<form method="POST" action="enroll.php">
    <label>Select Existing Student:</label>
    <select name="student_id" required>
        <option value="">-- Choose Student --</option>
        <?php foreach ($students as $st): ?>
            <option value="<?php echo $st['student_id']; ?>">
                <?php echo htmlspecialchars($st['full_name'] . ' (' . $st['email'] . ')'); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Select Class Section:</label>
    <select name="class_id" required>
        <option value="">-- Choose Class --</option>
        <?php foreach ($classList as $cl): ?>
            <option value="<?php echo $cl['class_id']; ?>">
                <?php echo htmlspecialchars($cl['course_name'] . ' (' . $cl['class_code'] . ') - Slots left: ' . $cl['slots']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Enroll Student</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>