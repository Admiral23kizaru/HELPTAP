<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'requester') {
    header('Location: ../includes/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ../requester/dashboard.php');
    exit();
}

$request_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if the request belongs to the user and is pending
$sql = "SELECT * FROM requests WHERE id = $request_id AND user_id = $user_id AND status = 'pending'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) === 1) {
    $delete = mysqli_query($conn, "DELETE FROM requests WHERE id = $request_id");
    if ($delete) {
        $_SESSION['success'] = 'Request deleted successfully.';
    } else {
        $_SESSION['error'] = 'Failed to delete request.';
    }
} else {
    $_SESSION['error'] = 'Request not found or cannot be deleted.';
}
header('Location: ../requester/dashboard.php');
exit(); 