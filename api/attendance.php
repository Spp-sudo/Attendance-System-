<?php
// =============================================
// attendance.php - Attendance API
// Handles: POST mark attendance, GET fetch report
// =============================================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json");

include "db.php";

$method = $_SERVER["REQUEST_METHOD"];

// ---- GET: Fetch attendance report (with student name via JOIN) ----
if ($method === "GET") {
    // Get optional filter parameters from URL
    $date_filter = isset($_GET["date"]) ? mysqli_real_escape_string($conn, $_GET["date"]) : "";
    $dept_filter = isset($_GET["department"]) ? mysqli_real_escape_string($conn, $_GET["department"]) : "";

    // SQL JOIN: combines attendance + students tables to show full report
    $sql = "SELECT
                a.attendance_id,
                s.student_id,
                s.usn,
                s.name,
                s.department,
                a.date,
                a.status
            FROM attendance a
            JOIN students s ON a.student_id = s.student_id";

    // Add WHERE clauses if filters are provided
    $conditions = [];
    if (!empty($date_filter)) {
        $conditions[] = "a.date = '$date_filter'";
    }
    if (!empty($dept_filter)) {
        $conditions[] = "s.department = '$dept_filter'";
    }
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY a.date DESC, s.name ASC";

    $result = mysqli_query($conn, $sql);

    $records = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = $row;
    }

    echo json_encode($records);
}

// ---- POST: Mark attendance for a student ----
elseif ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $student_id = intval($data["student_id"]); // intval prevents injection
    $date = mysqli_real_escape_string($conn, $data["date"]);
    $status = mysqli_real_escape_string($conn, $data["status"]); // 'Present' or 'Absent'

    // Basic validation
    if ($student_id <= 0 || empty($date) || empty($status)) {
        echo json_encode(["error" => "All fields are required."]);
        exit;
    }

    // Check if attendance already marked for this student on this date
    $check = "SELECT attendance_id FROM attendance WHERE student_id = $student_id AND date = '$date'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        // Update existing record
        $sql = "UPDATE attendance SET status = '$status' WHERE student_id = $student_id AND date = '$date'";
        $action = "updated";
    } else {
        // Insert new record
        $sql = "INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$status')";
        $action = "marked";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            "success" => true,
            "message" => "Attendance $action successfully."
        ]);
    } else {
        echo json_encode(["error" => "Failed: " . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>
