<?php
// admin/students.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$repo = new EnrollmentRepository($db);
$classesObj = new ClassSection($db);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $class_id  = (int)($_POST['class_id'] ?? 0);

    if (!empty($full_name) && $class_id > 0) {
        if ($repo->recordStudent($full_name, $email, $phone, $class_id)) {
            $message = "Student successfully recorded and enrolled!";
        } else {
            $error = "Enrollment failed: No slots available or system error.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

$classList = $classesObj->allWithCourse();
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Record New Student & Enroll</h2>

<?php if ($message): ?><div class="alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<form method="POST" action="students.php">
    <label>Full Name:</label>
    <input type="text" name="full_name" required placeholder="John Doe">

    <label>Email Address:</label>
    <input type="email" name="email" placeholder="john@example.com">

    <label>Phone Number:</label>
    <input type="text" name="phone" placeholder="555-0199">

    <label>Select Class Section:</label>
    <select name="class_id" required>
        <option value="">-- Choose Class --</option>
        <?php foreach ($classList as $cl): ?>
            <option value="<?php echo $cl['class_id']; ?>">
                <?php echo htmlspecialchars($cl['course_name'] . ' (' . $cl['class_code'] . ') - Slots left: ' . $cl['slots']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Record Student & Enroll</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>