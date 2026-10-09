<?php
// classes/EnrollmentRepository.php
class EnrollmentRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Record a NEW student AND enroll them into a class (Transactional)
    public function recordStudent($full_name, $email, $phone, $class_id) {
        try {
            $this->db->beginTransaction();

            // 1. Check available slots
            $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :class_id FOR UPDATE");
            $stmt->execute([':class_id' => $class_id]);
            $class = $stmt->fetch();

            if (!$class || $class['slots'] <= 0) {
                $this->db->rollBack();
                return false;
            }

            // 2. Insert Student
            $stmt = $this->db->prepare("INSERT INTO students (full_name, email, phone) VALUES (:full_name, :email, :phone)");
            $stmt->execute([
                ':full_name' => $full_name,
                ':email'     => $email,
                ':phone'     => $phone
            ]);
            $student_id = $this->db->lastInsertId();

            // 3. Insert Enrollment
            $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, class_id) VALUES (:student_id, :class_id)");
            $stmt->execute([
                ':student_id' => $student_id,
                ':class_id'   => $class_id
            ]);

            // 4. Decrement Slots
            $stmt = $this->db->prepare("UPDATE classes SET slots = slots - 1 WHERE class_id = :class_id");
            $stmt->execute([':class_id' => $class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    // Enroll an EXISTING student into a class (Transactional)
    public function enroll($student_id, $class_id) {
        try {
            $this->db->beginTransaction();

            // 1. Check available slots
            $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :class_id FOR UPDATE");
            $stmt->execute([':class_id' => $class_id]);
            $class = $stmt->fetch();

            if (!$class || $class['slots'] <= 0) {
                $this->db->rollBack();
                return false;
            }

            // 2. Insert Enrollment
            $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, class_id) VALUES (:student_id, :class_id)");
            $stmt->execute([
                ':student_id' => $student_id,
                ':class_id'   => $class_id
            ]);

            // 3. Decrement Slots
            $stmt = $this->db->prepare("UPDATE classes SET slots = slots - 1 WHERE class_id = :class_id");
            $stmt->execute([':class_id' => $class_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    // Cancel an enrollment and restore class slots (Transactional)
    public function cancel($enrollment_id) {
        try {
            $this->db->beginTransaction();

            // 1. Get enrollment details
            $stmt = $this->db->prepare("SELECT class_id, status FROM enrollments WHERE enrollment_id = :id FOR UPDATE");
            $stmt->execute([':id' => $enrollment_id]);
            $enrollment = $stmt->fetch();

            if (!$enrollment || $enrollment['status'] === 'cancelled') {
                $this->db->rollBack();
                return false;
            }

            // 2. Update status to cancelled
            $stmt = $this->db->prepare("UPDATE enrollments SET status = 'cancelled' WHERE enrollment_id = :id");
            $stmt->execute([':id' => $enrollment_id]);

            // 3. Restore slot
            $stmt = $this->db->prepare("UPDATE classes SET slots = slots + 1 WHERE class_id = :class_id");
            $stmt->execute([':class_id' => $enrollment['class_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    // Fetch all enrollments with multi-table JOINs
    public function allWithDetails() {
        $sql = "SELECT e.enrollment_id, e.enrollment_date, e.status,
                       s.full_name, s.email, s.phone,
                       c.class_code, c.schedule, c.instructor,
                       co.course_name, co.course_code
                FROM enrollments e
                JOIN students s ON e.student_id = s.student_id
                JOIN classes c ON e.class_id = c.class_id
                JOIN courses co ON c.course_id = co.course_id
                ORDER BY e.enrollment_id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}