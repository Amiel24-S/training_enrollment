<?php
// admin/enrollments.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$repo = new EnrollmentRepository($db);
$message = '';
$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['id'])) {
    $enrollment_id = (int)$_GET['id'];
    if ($repo->cancel($enrollment_id)) {
        $message = "Enrollment #$enrollment_id successfully cancelled and slot restored!";
    } else {
        $error = "Failed to cancel enrollment.";
    }
}

$rows = $repo->allWithDetails();
require_once __DIR__ . '/../includes/header.php';
?>

<h2>All Enrollments</h2>

<?php if ($message): ?><div class="alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Enrollment ID</th>
            <th>Student Name</th>
            <th>Course</th>
            <th>Class Code</th>
            <th>Schedule</th>
            <th>Enrollment Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['enrollment_id']); ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                <td><?php echo htmlspecialchars($row['class_code']); ?></td>
                <td><?php echo htmlspecialchars($row['schedule']); ?></td>
                <td><?php echo htmlspecialchars($row['enrollment_date']); ?></td>
                <td>
                    <span style="color: <?php echo $row['status'] === 'active' ? 'green' : 'red'; ?>; font-weight: bold;">
                        <?php echo htmlspecialchars(strtoupper($row['status'])); ?>
                    </span>
                </td>
                <td>
                    <?php if ($row['status'] === 'active'): ?>
                        <a href="enrollments.php?action=cancel&id=<?php echo $row['enrollment_id']; ?>" 
                           onclick="return confirm('Are you sure you want to cancel this enrollment?');" 
                           style="color: red; text-decoration: none; font-weight: bold;">
                           Cancel
                        </a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
            <tr><td colspan="8">No enrollment records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>