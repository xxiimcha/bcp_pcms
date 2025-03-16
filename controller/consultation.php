<?php
require_once '../database/config.php';

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'schedule':
            scheduleAppointment();
            break;
        case 'track':
            trackAppointment();
            break;
        case 'update_status':
            updateAppointmentStatus();
            break;
        case 'view_details':
            viewAppointmentDetails();
            break;
        default:
            echo json_encode(["success" => false, "message" => "Invalid action"]);
            break;
    }
} else {
    echo json_encode(["success" => false, "message" => "No action specified"]);
}

function scheduleAppointment()
{
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        return;
    }

    $full_name = mysqli_real_escape_string($conn, $_POST['name']);
    $birthdate = mysqli_real_escape_string($conn, $_POST['birthdate']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $consultation_type = mysqli_real_escape_string($conn, $_POST['consultationType']);
    $secondary_concern = isset($_POST['secondaryConcern']) ? mysqli_real_escape_string($conn, $_POST['secondaryConcern']) : null;
    $schedule_date = mysqli_real_escape_string($conn, $_POST['schedule_date']);
    $schedule_time = mysqli_real_escape_string($conn, $_POST['schedule_time']);
    $comments = mysqli_real_escape_string($conn, $_POST['message']);

    // Generate unique reference code
    $reference_code = generateReferenceCode($consultation_type);

    // Insert into database using raw MySQL query
    $query = "INSERT INTO consultations (reference_code, full_name, birthdate, email, phone, consultation_type, secondary_concern, schedule_date, schedule_time, comments, status) 
              VALUES ('$reference_code', '$full_name', '$birthdate', '$email', '$phone', '$consultation_type', '$secondary_concern', '$schedule_date', '$schedule_time', '$comments', 'Pending')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "reference_code" => $reference_code]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
}

function trackAppointment()
{
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        return;
    }

    $reference_code = mysqli_real_escape_string($conn, $_POST['reference_code']);

    $query = "SELECT * FROM consultations WHERE reference_code = '$reference_code'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo json_encode([
            "success" => true,
            "status" => $row['status'],
            "reference_code" => $row['reference_code'],
            "schedule_date" => $row['schedule_date'],
            "schedule_time" => $row['schedule_time'],
            "comments" => $row['comments']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Reference code not found."]);
    }

    mysqli_close($conn);
}

function generateReferenceCode($consultation_type)
{
    global $conn;
    
    $prefix = strtoupper(substr($consultation_type, 0, 5));
    $prefix = preg_replace('/[^A-Z]/', '', $prefix); // Ensure only letters

    $sql = "SELECT reference_code FROM consultations WHERE reference_code LIKE 'CONSULT-$prefix-%' ORDER BY created_at DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_code = substr($row['reference_code'], -4);
        $letter = $last_code[0];
        $number = (int)substr($last_code, 1);

        if ($number < 999) {
            $new_number = str_pad($number + 1, 3, '0', STR_PAD_LEFT);
            return "CONSULT-$prefix-$letter$new_number";
        } else {
            $new_letter = chr(ord($letter) + 1);
            return "CONSULT-$prefix-$new_letter" . "001";
        }
    } else {
        return "CONSULT-$prefix-A001";
    }
}


/**
 * Updates the status of an appointment and logs the change in consultation_history.
 */

 function updateAppointmentStatus() {
    global $conn;

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        return;
    }

    $reference_code = mysqli_real_escape_string($conn, $_POST['reference_code']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $remarks = isset($_POST['remarks']) ? mysqli_real_escape_string($conn, $_POST['remarks']) : 'No remarks';

    $sql = "SELECT id FROM consultations WHERE reference_code = '$reference_code' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $consultation_id = $row['id'];

        $updateQuery = "UPDATE consultations SET status = '$status' WHERE id = '$consultation_id'";
        if (mysqli_query($conn, $updateQuery)) {
            $historyQuery = "INSERT INTO consultation_history (consultation_id, status, remarks, updated_at) 
                             VALUES ('$consultation_id', '$status', '$remarks', NOW())";
            mysqli_query($conn, $historyQuery);

            echo json_encode(["success" => true, "message" => "Appointment updated successfully"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid reference code"]);
    }
}

function viewAppointmentDetails() {
    global $conn;
    
    $reference_code = mysqli_real_escape_string($conn, $_POST['reference_code']);
    $query = "SELECT * FROM consultations WHERE reference_code = '$reference_code' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode(["success" => true, "data" => $data]);
    } else {
        echo json_encode(["success" => false, "message" => "Appointment not found"]);
    }
}
?>
