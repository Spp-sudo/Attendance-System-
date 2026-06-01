<?php
// =============================================
// import_attendance.php - Import attendance CSV rows through JSON
// Expected JSON: { records: [ { usn, date, status }, ... ] }
// Existing attendance for same USN + date is updated.
// =============================================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed."]);
    exit;
}

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$records = isset($data["records"]) && is_array($data["records"]) ? $data["records"] : [];

if (count($records) === 0) {
    echo json_encode(["success" => false, "error" => "No records received."]);
    exit;
}

$saved = 0;
$failed = 0;
$errors = [];

foreach ($records as $index => $record) {
    $rowNumber = $index + 2; // +2 because CSV has header row

    $usn = isset($record["usn"]) ? mysqli_real_escape_string($conn, strtoupper(trim($record["usn"]))) : "";
    $date = isset($record["date"]) ? mysqli_real_escape_string($conn, trim($record["date"])) : "";
    $status = isset($record["status"]) ? trim($record["status"]) : "";

    if (strcasecmp($status, "Present") === 0) {
        $status = "Present";
    } elseif (strcasecmp($status, "Absent") === 0) {
        $status = "Absent";
    } else {
        $failed++;
        $errors[] = "Row $rowNumber: status must be Present or Absent.";
        continue;
    }

    if (empty($usn) || empty($date)) {
        $failed++;
        $errors[] = "Row $rowNumber: USN and Date are required.";
        continue;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $failed++;
        $errors[] = "Row $rowNumber: date must be YYYY-MM-DD.";
        continue;
    }

    $studentSql = "SELECT student_id FROM students WHERE usn = '$usn' LIMIT 1";
    $studentResult = mysqli_query($conn, $studentSql);

    if (!$studentResult || mysqli_num_rows($studentResult) === 0) {
        $failed++;
        $errors[] = "Row $rowNumber: USN $usn not found in students table.";
        continue;
    }

    $student = mysqli_fetch_assoc($studentResult);
    $student_id = intval($student["student_id"]);
    $safeStatus = mysqli_real_escape_string($conn, $status);

    $checkSql = "SELECT attendance_id FROM attendance WHERE student_id = $student_id AND date = '$date' LIMIT 1";
    $checkResult = mysqli_query($conn, $checkSql);

    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        $sql = "UPDATE attendance SET status = '$safeStatus' WHERE student_id = $student_id AND date = '$date'";
    } else {
        $sql = "INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$safeStatus')";
    }

    if (mysqli_query($conn, $sql)) {
        $saved++;
    } else {
        $failed++;
        $errors[] = "Row $rowNumber: database error - " . mysqli_error($conn);
    }
}

echo json_encode([
    "success" => true,
    "saved" => $saved,
    "failed" => $failed,
    "errors" => array_slice($errors, 0, 10)
]);

mysqli_close($conn);
?>
