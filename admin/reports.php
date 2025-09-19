<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';
requireAdmin();

$query = "SELECT r.*, u.name as reported_name, u.email as reported_email, rep.name as reporter_name FROM reports r JOIN users u ON r.reported_user_id = u.id JOIN users rep ON r.reporter_id = rep.id ORDER BY r.created_at DESC";
$reports = fetch_rows($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reports - HelpTap Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Reports Management</h2>
    <a href="dashboard.php" class="btn btn-link mb-3">&larr; Back to Dashboard</a>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Reported User</th>
                        <th>Reporter</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['reported_name']); ?> (<?php echo htmlspecialchars($report['reported_email']); ?>)</td>
                        <td><?php echo htmlspecialchars($report['reporter_name']); ?></td>
                        <td><?php echo htmlspecialchars($report['reason']); ?></td>
                        <td><span class="badge bg-<?php echo $report['status'] === 'resolved' ? 'success' : ($report['status'] === 'pending' ? 'warning' : 'danger'); ?>"><?php echo htmlspecialchars(ucfirst($report['status'])); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info disabled"><i class="fas fa-eye"></i></a>
                            <a href="#" class="btn btn-sm btn-success disabled"><i class="fas fa-check"></i></a>
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