<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';
requireAdmin();

// Fetch all requests with requester and helper info
$query = "SELECT r.*, u.name as requester_name, h.name as helper_name FROM requests r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN users h ON r.helper_id = h.id ORDER BY r.created_at DESC";
$requests = fetch_rows($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Requests - HelpTap Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Request Management</h2>
    <a href="dashboard.php" class="btn btn-link mb-3">&larr; Back to Dashboard</a>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Requester</th>
                        <th>Helper</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['title']); ?></td>
                        <td><?php echo htmlspecialchars($request['requester_name']); ?></td>
                        <td><?php echo $request['helper_name'] ? htmlspecialchars($request['helper_name']) : '-'; ?></td>
                        <td><span class="badge bg-<?php echo $request['status'] === 'completed' ? 'success' : ($request['status'] === 'pending' ? 'warning' : 'primary'); ?>"><?php echo htmlspecialchars(ucfirst($request['status'])); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
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