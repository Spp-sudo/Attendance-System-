<?php
// =============================================
// students.php - Student CRUD API
// Handles: GET all, POST add, PUT edit, DELETE remove
// =============================================

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

include "db.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $sql = "SELECT student_id, usn, name, department FROM students ORDER BY usn ASC";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo json_encode(["success" => false, "error" => "Failed to load students: " . mysqli_error($conn)]);
        exit;
    }

    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    echo json_encode($students);
}

elseif ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $usn = isset($data["usn"]) ? mysqli_real_escape_string($conn, strtoupper(trim($data["usn"]))) : "";
    $name = isset($data["name"]) ? mysqli_real_escape_string($conn, trim($data["name"])) : "";
    $department = isset($data["department"]) ? mysqli_real_escape_string($conn, strtoupper(trim($data["department"]))) : "";

    if (empty($usn) || empty($name) || empty($department)) {
        echo json_encode(["success" => false, "error" => "USN, name, and department are required."]);
        exit;
    }

    $check = "SELECT student_id FROM students WHERE usn = '$usn'";
    $check_result = mysqli_query($conn, $check);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        echo json_encode(["success" => false, "error" => "This USN already exists."]);
        exit;
    }

    $sql = "INSERT INTO students (usn, name, department) VALUES ('$usn', '$name', '$department')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Student added successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to add student: " . mysqli_error($conn)]);
    }
}

elseif ($method === "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    $student_id = isset($data["student_id"]) ? intval($data["student_id"]) : 0;
    $usn = isset($data["usn"]) ? mysqli_real_escape_string($conn, strtoupper(trim($data["usn"]))) : "";
    $name = isset($data["name"]) ? mysqli_real_escape_string($conn, trim($data["name"])) : "";
    $department = isset($data["department"]) ? mysqli_real_escape_string($conn, strtoupper(trim($data["department"]))) : "";

    if ($student_id <= 0 || empty($usn) || empty($name) || empty($department)) {
        echo json_encode(["success" => false, "error" => "Student ID, USN, name, and department are required."]);
        exit;
    }

    $check = "SELECT student_id FROM students WHERE usn = '$usn' AND student_id != $student_id";
    $check_result = mysqli_query($conn, $check);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
        echo json_encode(["success" => false, "error" => "This USN already belongs to another student."]);
        exit;
    }

    $sql = "UPDATE students SET usn = '$usn', name = '$name', department = '$department' WHERE student_id = $student_id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Student updated successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to update student: " . mysqli_error($conn)]);
    }
}

elseif ($method === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);
    $student_id = isset($data["student_id"]) ? intval($data["student_id"]) : 0;

    if ($student_id <= 0) {
        echo json_encode(["success" => false, "error" => "Invalid student ID."]);
        exit;
    }

    $sql = "DELETE FROM students WHERE student_id = $student_id";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true, "message" => "Student deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => "Failed to delete student: " . mysqli_error($conn)]);
    }
}

else {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Method not allowed."]);
}

mysqli_close($conn);
?>
