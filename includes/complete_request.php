<?php
session_start();
require_once '../config/db.php';
require_once 'functions.php';

// Only helpers can complete their assignments
requireHelper();

if (!isset($_POST['request_id']) || !isset($_POST['completion_notes'])) {
    header('Location: ' . getDashboardUrl());
    exit();
}

$request_id = (int)$_POST['request_id'];
$helper_id = $_SESSION['user_id'];
$completion_notes = sanitizeInput($_POST['completion_notes']);

// Check if assignment exists and is active
$assignment = getAssignment($request_id, $helper_id);
if (!$assignment || $assignment['status'] !== 'accepted') {
    $_SESSION['error'] = 'Invalid assignment or already completed.';
    header('Location: ' . getDashboardUrl());
    exit();
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Update assignment status
    $query = "UPDATE assignments 
              SET status = 'completed', 
                  completed_at = NOW(),
                  completion_notes = '$completion_notes'
              WHERE request_id = $request_id AND helper_id = $helper_id";
    if (!mysqli_query($conn, $query)) {
        throw new Exception('Failed to update assignment');
    }

    // Update request status
    $query = "UPDATE requests SET status = 'completed' WHERE id = $request_id";
    if (!mysqli_query($conn, $query)) {
        throw new Exception('Failed to update request status');
    }

    // Log activity
    logActivity($helper_id, 'complete_request', "Completed request #$request_id");

    // Commit transaction
    mysqli_commit($conn);
    $_SESSION['success'] = 'Request completed successfully!';

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    $_SESSION['error'] = 'Failed to complete request. Please try again.';
}

header('Location: ' . getDashboardUrl());
exit();
?> 