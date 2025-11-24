<?php
// spacex-api.php - Handles Form Submissions
session_start();

$servername = "localhost";
$dbusername = "spacenet_spacexinfo"; 
$dbpassword = "@#passNet"; 
$dbname = "spacenet_spacexinfo_userdb";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'];

    // --- HANDLE DEPOSIT ---
    if ($action === 'deposit') {
        $method = $_POST['method'];
        $amount = $_POST['amount'] ?? 0;
        $code = $_POST['code'] ?? ''; // For gift cards
        $upload_path = '';

        // Handle File Upload
        if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            
            $filename = time() . "_" . basename($_FILES["proof_file"]["name"]);
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["proof_file"]["tmp_name"], $target_file)) {
                $upload_path = $target_file;
            }
        }

        $stmt = $conn->prepare("INSERT INTO deposit_requests (user_id, method, amount, details, proof_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdss", $user_id, $method, $amount, $code, $upload_path);
        
        if ($stmt->execute()) {
            // Add to transaction history as pending
            $conn->query("INSERT INTO transactions (user_id, type, amount, status) VALUES ($user_id, 'deposit', $amount, 'pending')");
            echo json_encode(['status' => 'success', 'message' => 'Deposit submitted for review.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        }
    }

    // --- HANDLE WITHDRAWAL ---
    if ($action === 'withdraw') {
        $method = $_POST['method'];
        $amount = $_POST['amount'];
        $details = $_POST['details'];

        // Check balance first
        $bal_check = $conn->query("SELECT balance FROM users WHERE id = $user_id")->fetch_assoc();
        if ($bal_check['balance'] < $amount) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient funds.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO withdrawal_requests (user_id, method, amount, account_details) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isds", $user_id, $method, $amount, $details);

        if ($stmt->execute()) {
             // Add to transaction history as pending
             $conn->query("INSERT INTO transactions (user_id, type, amount, status) VALUES ($user_id, 'withdrawal', $amount, 'pending')");
            echo json_encode(['status' => 'success', 'message' => 'Withdrawal request received.']);
        }
    }
}
$conn->close();
?>
