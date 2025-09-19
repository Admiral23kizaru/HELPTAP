<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Require requester role
requireRequester();

// Get request ID
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get request details
$query = "SELECT r.*, h.name as helper_name, h.email as helper_email, h.phone as helper_phone 
          FROM requests r 
          LEFT JOIN users h ON r.helper_id = h.id 
          WHERE r.id = $request_id AND r.user_id = " . (int)$_SESSION['user_id'];
$request = get_row($query);

// If request not found or doesn't belong to user
if (!$request) {
    header('Location: dashboard.php');
    exit();
}

// Get request history
$query = "SELECT l.*, u.name as user_name 
          FROM logs l 
          LEFT JOIN users u ON l.user_id = u.id 
          WHERE l.request_id = $request_id 
          ORDER BY l.created_at DESC";
$history = get_rows($query);
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
        .request-header {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
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
                        <a class="nav-link" href="dashboard.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Request Details</h2>
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <div class="request-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="mb-2"><?php echo htmlspecialchars($request['title']); ?></h3>
                            <span class="badge bg-<?php 
                                echo $request['status'] === 'completed' ? 'success' : 
                                    ($request['status'] === 'pending' ? 'warning' : 'danger'); 
                            ?>">
                                <?php echo ucfirst($request['status']); ?>
                            </span>
                        </div>
                        <div class="text-end">
                            <div class="text-white-50">Created</div>
                            <div><?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Request Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Description</label>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($request['description'])); ?></p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Location</label>
                                        <p class="mb-0"><?php echo htmlspecialchars($request['location']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Urgency</label>
                                        <p class="mb-0">
                                            <span class="badge bg-<?php 
                                                echo $request['urgency'] === 'high' ? 'danger' : 
                                                    ($request['urgency'] === 'medium' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo ucfirst($request['urgency']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($request['helper_name']): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Helper Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Name</label>
                                        <p class="mb-0"><?php echo htmlspecialchars($request['helper_name']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Email</label>
                                        <p class="mb-0"><?php echo htmlspecialchars($request['helper_email']); ?></p>
                                    </div>
                                </div>
                                <?php if ($request['helper_phone']): ?>
                                <div class="mt-3">
                                    <label class="form-label text-muted">Phone</label>
                                    <p class="mb-0"><?php echo htmlspecialchars($request['helper_phone']); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
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
                                                    <small class="text-muted">
                                                        <?php echo $log['user_name'] ? htmlspecialchars($log['user_name']) : 'System'; ?>
                                                    </small>
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
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 