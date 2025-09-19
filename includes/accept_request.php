<?php
session_start();
require_once '../config/db.php';
require_once 'functions.php';

// Only helpers can accept requests
requireHelper();

if (!isset($_POST['request_id'])) {
    header('Location: ' . getDashboardUrl());
    exit();
}

$request_id = (int)$_POST['request_id'];
$helper_id = $_SESSION['user_id'];

// Check if request exists and is pending
$request = getRequest($request_id);
if (!$request || $request['status'] !== 'pending') {
    $_SESSION['error'] = 'Request is no longer available.';
    header('Location: ' . getDashboardUrl());
    exit();
}

// Check if helper already has an assignment for this request
$existing = getAssignment($request_id, $helper_id);
if ($existing) {
    $_SESSION['error'] = 'You have already accepted this request.';
    header('Location: ' . getDashboardUrl());
    exit();
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Create assignment
    $query = "INSERT INTO assignments (request_id, helper_id, status, accepted_at) 
              VALUES ($request_id, $helper_id, 'accepted', NOW())";
    if (!mysqli_query($conn, $query)) {
        throw new Exception('Failed to create assignment');
    }

    // Update request status
    $query = "UPDATE requests SET status = 'assigned' WHERE id = $request_id";
    if (!mysqli_query($conn, $query)) {
        throw new Exception('Failed to update request status');
    }

    // Log activity
    logActivity($helper_id, 'accept_request', "Accepted request #$request_id");

    // Commit transaction
    mysqli_commit($conn);
    $_SESSION['success'] = 'Request accepted successfully!';

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    $_SESSION['error'] = 'Failed to accept request. Please try again.';
}

header('Location: ' . getDashboardUrl());
exit();
?> 