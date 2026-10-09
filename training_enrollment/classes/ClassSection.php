<?php
// classes/ClassSection.php
class ClassSection {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function allWithCourse() {
        $sql = "SELECT c.*, co.course_name, co.course_code 
                FROM classes c 
                JOIN courses co ON c.course_id = co.course_id 
                ORDER BY c.class_id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create($course_id, $code, $schedule, $instructor, $slots) {
        $stmt = $this->db->prepare("INSERT INTO classes (course_id, class_code, schedule, instructor, slots) VALUES (:course_id, :code, :schedule, :instructor, :slots)");
        return $stmt->execute([
            ':course_id'  => $course_id,
            ':code'       => $code,
            ':schedule'   => $schedule,
            ':instructor' => $instructor,
            ':slots'      => $slots
        ]);
    }

    public function getSlots($class_id) {
        $stmt = $this->db->prepare("SELECT slots FROM classes WHERE class_id = :class_id");
        $stmt->execute([':class_id' => $class_id]);
        $row = $stmt->fetch();
        return $row ? (int)$row['slots'] : 0;
    }
}