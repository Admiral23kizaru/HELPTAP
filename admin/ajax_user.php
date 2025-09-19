<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';
header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$user_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($action === 'view' && $user_id) {
    $user = fetch_row("SELECT * FROM users WHERE id = $user_id");
    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
    exit();
}

if ($action === 'edit' && $user_id) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $role = sanitizeInput($_POST['role']);
    $data = ['name' => $name, 'email' => $email, 'role' => $role];
    $where = "id = $user_id";
    if (updateRecord('users', $data, $where)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update user']);
    }
    exit();
}

if ($action === 'delete' && $user_id) {
    if (deleteRecord('users', "id = $user_id")) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
    }
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid request']); 