<?php
session_start();
require_once '../config/db.php';
require_once 'functions.php';

// Only helpers can cancel their assignments
requireHelper();

if (!isset($_POST['request_id'])) {
    header('Location: ' . getDashboardUrl());
    exit();
}

$request_id = (int)$_POST['request_id'];
$helper_id = $_SESSION['user_id'];

// Check if assignment exists and is active
$assignment = getAssignment($request_id, $helper_id);
if (!$assignment || $assignment['status'] !== 'accepted') {
    $_SESSION['error'] = 'Invalid assignment or already cancelled.';
    header('Location: ' . getDashboardUrl());
    exit();
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Update assignment status
    $query = "UPDATE assignments 
              SET status = 'cancelled', completed_at = NOW() 
              WHERE request_id = $request_id AND helper_id = $helper_id";
    if (!mysqli_query($conn, $query)) {
        throw new Exception('Failed to update assignment');
    }

    // Update request status back to pending
    $query = "UPDATE requests SET status = 'pending' WHERE id = $request_id";
    if (!mysqli_query($conn, $query)) {
        throw new Exception('Failed to update request status');
    }

    // Log activity
    logActivity($helper_id, 'cancel_request', "Cancelled request #$request_id");

    // Commit transaction
    mysqli_commit($conn);
    $_SESSION['success'] = 'Request cancelled successfully!';

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    $_SESSION['error'] = 'Failed to cancel request. Please try again.';
}

header('Location: ' . getDashboardUrl());
exit();
?> 