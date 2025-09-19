<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

// Get activity logs
$query = "SELECT l.*, u.name as user_name, u.role as user_role 
          FROM logs l 
          LEFT JOIN users u ON l.user_id = u.id 
          ORDER BY l.created_at DESC";
$logs = get_rows($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity History - HelpTap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 1rem;
            margin: 0.2rem 0;
            border-radius: 0.5rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            background: #3498db;
            color: white;
        }
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20px;
            height: 100%;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item {
            position: relative;
            padding-left: 50px;
            margin-bottom: 30px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #3498db;
            border: 2px solid white;
        }
        .timeline-item .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .timeline-item .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.125);
            padding: 1rem;
        }
        .timeline-item .card-body {
            padding: 1rem;
        }
        .timeline-item .time {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .timeline-item .user-info {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .timeline-item .user-info .badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">HelpTap Admin</h4>
                    <div class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-chart-line me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a class="nav-link" href="requests.php">
                            <i class="fas fa-hands-helping me-2"></i>Requests
                        </a>
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-flag me-2"></i>Reports
                        </a>
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <a class="nav-link active" href="history.php">
                            <i class="fas fa-history me-2"></i>History
                        </a>
                        <a class="nav-link" href="../includes/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Activity History</h2>
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <div class="timeline">
                    <?php foreach ($logs as $log): ?>
                    <div class="timeline-item">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($log['action']); ?></h6>
                                    <div class="user-info mt-1">
                                        <?php if ($log['user_name']): ?>
                                            <span class="badge bg-<?php echo $log['user_role'] === 'admin' ? 'danger' : ($log['user_role'] === 'helper' ? 'success' : 'primary'); ?>">
                                                <?php echo ucfirst($log['user_role']); ?>
                                            </span>
                                            <?php echo htmlspecialchars($log['user_name']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="time">
                                    <?php echo date('M d, Y H:i', strtotime($log['created_at'])); ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><?php echo htmlspecialchars($log['details']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 