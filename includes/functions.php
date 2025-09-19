<?php
// Session and role management
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isHelper() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'helper';
}

function isRequester() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'requester';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../index.php');
        exit();
    }
}

function requireHelper() {
    requireLogin();
    if (!isHelper()) {
        header('Location: ../index.php');
        exit();
    }
}

function requireRequester() {
    requireLogin();
    if (!isRequester()) {
        header('Location: ../index.php');
        exit();
    }
}

// Database operations
function getRequest($request_id) {
    global $conn;
    $request_id = (int)$request_id;
    $query = "SELECT r.*, u.name as requester_name 
              FROM requests r 
              JOIN users u ON r.user_id = u.id 
              WHERE r.id = $request_id";
    return fetch_row($query);
}

function getAssignment($request_id, $helper_id) {
    global $conn;
    $request_id = (int)$request_id;
    $helper_id = (int)$helper_id;
    $query = "SELECT * FROM assignments 
              WHERE request_id = $request_id AND helper_id = $helper_id";
    return fetch_row($query);
}

function logActivity($user_id, $action, $details) {
    global $conn;
    $user_id = (int)$user_id;
    $action = sanitizeInput($action);
    $details = sanitizeInput($details);
    $query = "INSERT INTO logs (user_id, action, details) 
              VALUES ($user_id, '$action', '$details')";
    return run_query($query);
}

// Input validation
function validateRequest($title, $description) {
    $errors = array();
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    return $errors;
}

// Security
function sanitizeInput($input) {
    global $conn;
    return mysqli_real_escape_string($conn, $input);
}

// Navigation
function getDashboardUrl() {
    if (isAdmin()) {
        return '../admin/dashboard.php';
    } elseif (isHelper()) {
        return '../helper/dashboard.php';
    } elseif (isRequester()) {
        return '../requester/dashboard.php';
    }
    return 'login.php';
}

// Helper functions for database operations
function insertRecord($table, $data) {
    global $conn;
    $fields = array_keys($data);
    $values = array_map('sanitizeInput', array_values($data));
    
    $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") 
            VALUES ('" . implode("', '", $values) . "')";
    return run_query($sql);
}

function updateRecord($table, $data, $where) {
    global $conn;
    $sets = array();
    foreach ($data as $field => $value) {
        $sets[] = "$field = '" . sanitizeInput($value) . "'";
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $sets) . " WHERE $where";
    return run_query($sql);
}

function deleteRecord($table, $where) {
    global $conn;
    $sql = "DELETE FROM $table WHERE $where";
    return run_query($sql);
}

// Database query helpers
function fetch_row($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn) . " in query: " . $query);
        return false;
    }
    return mysqli_fetch_assoc($result);
}

function fetch_rows($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn) . " in query: " . $query);
        return array();
    }
    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function run_query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("MySQL Error: " . mysqli_error($conn) . " in query: " . $query);
        return false;
    }
    return true;
}

// Request status helpers
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function formatDateTime($date) {
    return date('M d, Y H:i', strtotime($date));
}

// User role helpers
function getUserRoleBadgeClass($role) {
    switch ($role) {
        case 'admin':
            return 'danger';
        case 'helper':
            return 'success';
        case 'requester':
            return 'primary';
        default:
            return 'secondary';
    }
}

// Request helpers
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// File helpers
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isAllowedFileType($filename) {
    $allowed = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx');
    return in_array(getFileExtension($filename), $allowed);
}

function generateUniqueFilename($filename) {
    $ext = getFileExtension($filename);
    return uniqid() . '.' . $ext;
}

// Error handling
function handleError($message, $redirect = null) {
    $_SESSION['error'] = $message;
    if ($redirect) {
        header('Location: ' . $redirect);
        exit();
    }
}

function handleSuccess($message, $redirect = null) {
    $_SESSION['success'] = $message;
    if ($redirect) {
        header('Location: ' . $redirect);
        exit();
    }
}

// Session helpers
function clearSessionMessages() {
    unset($_SESSION['error']);
    unset($_SESSION['success']);
}

// Validation helpers
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

function validatePassword($password) {
    return strlen($password) >= 6;
}

// Security helpers
function generateToken() {
    return bin2hex(random_bytes(32));
}

function verifyToken($token) {
    return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
}

function setToken() {
    $_SESSION['token'] = generateToken();
    return $_SESSION['token'];
}

// Notification helpers
function sendNotification($user_id, $title, $message) {
    $data = array(
        'user_id' => (int)$user_id,
        'title' => sanitizeInput($title),
        'message' => sanitizeInput($message),
        'created_at' => date('Y-m-d H:i:s')
    );
    return insertRecord('notifications', $data);
}

function getUnreadNotifications($user_id) {
    $query = "SELECT * FROM notifications 
              WHERE user_id = " . (int)$user_id . " 
              AND read_at IS NULL 
              ORDER BY created_at DESC";
    return fetch_rows($query);
}

function markNotificationAsRead($notification_id) {
    $data = array('read_at' => date('Y-m-d H:i:s'));
    $where = "id = " . (int)$notification_id;
    return updateRecord('notifications', $data, $where);
}
?> 