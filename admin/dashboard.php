<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

// Get statistics
$stats = array(
    'total_users' => 0,
    'total_requests' => 0,
    'pending_requests' => 0,
    'completed_requests' => 0,
    'total_helpers' => 0,
    'total_requesters' => 0
);

// Get total users
$query = "SELECT COUNT(*) as count FROM users WHERE role != 'admin'";
$result = fetch_row($query);
$stats['total_users'] = $result['count'];

// Get total requests
$query = "SELECT COUNT(*) as count FROM requests";
$result = fetch_row($query);
$stats['total_requests'] = $result['count'];

// Get pending requests
$query = "SELECT COUNT(*) as count FROM requests WHERE status = 'pending'";
$result = fetch_row($query);
$stats['pending_requests'] = $result['count'];

// Get completed requests
$query = "SELECT COUNT(*) as count FROM requests WHERE status = 'completed'";
$result = fetch_row($query);
$stats['completed_requests'] = $result['count'];

// Get total helpers
$query = "SELECT COUNT(*) as count FROM users WHERE role = 'helper'";
$result = fetch_row($query);
$stats['total_helpers'] = $result['count'];

// Get total requesters
$query = "SELECT COUNT(*) as count FROM users WHERE role = 'requester'";
$result = fetch_row($query);
$stats['total_requesters'] = $result['count'];

// Get recent requests
$query = "SELECT r.*, u.name as requester_name, h.name as helper_name 
          FROM requests r 
          LEFT JOIN users u ON r.user_id = u.id 
          LEFT JOIN users h ON r.helper_id = h.id 
          ORDER BY r.created_at DESC 
          LIMIT 5";
$recent_requests = fetch_rows($query);

// Get recent users
$query = "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC LIMIT 5";
$recent_users = fetch_rows($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HelpTap</title>
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
        .stat-card {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2rem;
            opacity: 0.8;
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
                        <a class="nav-link active" href="dashboard.php">
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
                        <a class="nav-link" href="../includes/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div class="text-muted">
                        Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Users</h6>
                                        <h2 class="mb-0"><?php echo $stats['total_users']; ?></h2>
                                    </div>
                                    <i class="fas fa-users stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Requests</h6>
                                        <h2 class="mb-0"><?php echo $stats['total_requests']; ?></h2>
                                    </div>
                                    <i class="fas fa-hands-helping stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Pending Requests</h6>
                                        <h2 class="mb-0"><?php echo $stats['pending_requests']; ?></h2>
                                    </div>
                                    <i class="fas fa-clock stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Requests -->
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Recent Requests</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Requester</th>
                                                <th>Helper</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_requests as $request): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($request['title']); ?></td>
                                                <td><?php echo htmlspecialchars($request['requester_name']); ?></td>
                                                <td><?php echo $request['helper_name'] ? htmlspecialchars($request['helper_name']) : '-'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $request['status'] === 'completed' ? 'success' : 
                                                            ($request['status'] === 'pending' ? 'warning' : 'danger'); 
                                                    ?>">
                                                        <?php echo ucfirst($request['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Recent Users</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recent_users as $user): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo ucfirst($user['role']); ?> • 
                                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 