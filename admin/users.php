<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';
requireAdmin();

// Fetch all users except admin
$query = "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC";
$users = fetch_rows($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - HelpTap Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">User Management</h2>
    <a href="dashboard.php" class="btn btn-link mb-3">&larr; Back to Dashboard</a>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="badge bg-<?php echo $user['role'] === 'helper' ? 'success' : 'primary'; ?>"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info disabled"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-sm btn-warning disabled"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-sm btn-danger disabled"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 