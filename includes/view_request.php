<?php
session_start();
require_once '../config/db.php';
require_once 'functions.php';

// Require login for all users
requireLogin();

if (!isset($_GET['id'])) {
    handleError('Request ID is required.', getDashboardUrl());
}

$request_id = (int)$_GET['id'];
$request = getRequest($request_id);

if (!$request) {
    handleError('Request not found.', getDashboardUrl());
}

// Get assignment details if helper is viewing
$assignment = null;
if (isHelper()) {
    $assignment = getAssignment($request_id, $_SESSION['user_id']);
}

// Get request history
$query = "SELECT l.*, u.name as user_name, u.role as user_role 
          FROM logs l 
          LEFT JOIN users u ON l.user_id = u.id 
          WHERE l.request_id = $request_id 
          ORDER BY l.created_at DESC";
$history = fetch_rows($query);

// Get user info
$query = "SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'];
$user = fetch_row($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Request - HelpTap</title>
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
        .profile-header {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .request-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            transition: transform 0.3s;
        }
        .request-card:hover {
            transform: translateY(-5px);
        }
        .timeline {
            position: relative;
            padding: 1rem 0;
        }
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1.5rem;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        .timeline-item:last-child:before {
            bottom: 50%;
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">HelpTap</h4>
                    <div class="nav flex-column">
                        <a class="nav-link" href="<?php echo getDashboardUrl(); ?>">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="profile.php">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <a class="nav-link" href="history.php">
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
                <div class="profile-header">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=random" 
                             alt="Profile" class="profile-avatar me-4">
                        <div>
                            <h2 class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h2>
                            <p class="mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Request Details</h2>
                    <a href="<?php echo getDashboardUrl(); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <div class="row">
                    <!-- Request Details -->
                    <div class="col-md-8 mb-4">
                        <div class="card request-card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($request['title']); ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h6 class="text-muted mb-2">Description</h6>
                                    <p><?php echo nl2br(htmlspecialchars($request['description'])); ?></p>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Status</h6>
                                        <span class="badge bg-<?php echo getStatusBadgeClass($request['status']); ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Created</h6>
                                        <p class="mb-0"><?php echo formatDateTime($request['created_at']); ?></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Requester</h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($request['requester_name']); ?></p>
                                    </div>
                                    <?php if ($request['helper_id']): ?>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Helper</h6>
                                        <p class="mb-0"><?php echo htmlspecialchars($request['helper_name']); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Request History -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Request History</h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <?php foreach ($history as $log): ?>
                                    <div class="timeline-item">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($log['action']); ?></h6>
                                                    <div class="user-info mt-1">
                                                        <?php if ($log['user_name']): ?>
                                                            <span class="badge bg-<?php echo getUserRoleBadgeClass($log['user_role']); ?>">
                                                                <?php echo ucfirst($log['user_role']); ?>
                                                            </span>
                                                            <?php echo htmlspecialchars($log['user_name']); ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">System</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="time">
                                                    <?php echo formatDateTime($log['created_at']); ?>
                                                </div>
                                            </div>
                                            <?php if ($log['details']): ?>
                                            <div class="card-body">
                                                <p class="mb-0"><?php echo htmlspecialchars($log['details']); ?></p>
                                            </div>
                                            <?php endif; ?>
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