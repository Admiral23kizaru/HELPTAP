<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Require helper role
requireHelper();

// Get available requests (pending requests not assigned to this helper)
$query = "SELECT r.*, u.name as requester_name
          FROM requests r
          JOIN users u ON r.user_id = u.id
          WHERE r.status = 'pending'
          AND r.id NOT IN (SELECT request_id FROM assignments WHERE helper_id = " . (int)$_SESSION['user_id'] . ")
          ORDER BY r.created_at DESC";
$available = fetch_rows($query);

// Get helper's assignments
$query = "SELECT r.*, u.name as requester_name, a.status as assignment_status
          FROM assignments a
          JOIN requests r ON a.request_id = r.id
          JOIN users u ON r.user_id = u.id
          WHERE a.helper_id = " . (int)$_SESSION['user_id'] . "
          ORDER BY a.accepted_at DESC";
$assignments = fetch_rows($query);

// Get helper info
$query = "SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'];
$user = fetch_row($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helper Dashboard - HelpTap</title>
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
                        <a class="nav-link active" href="dashboard.php">
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

                <div class="row">
                    <!-- Available Requests -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Available Requests</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($available)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>No available requests at the moment.
                                    </div>
                                <?php else: ?>
                                    <div class="row g-4">
                                        <?php foreach ($available as $request): ?>
                                            <div class="col-12">
                                                <div class="card request-card">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($request['title']); ?></h5>
                                                        <p class="card-text text-muted">
                                                            <?php echo htmlspecialchars(substr($request['description'], 0, 100)) . '...'; ?>
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-warning">Pending</span>
                                                            <small class="text-muted">
                                                                <?php echo date('M d, Y', strtotime($request['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between">
                                                            <a href="view_request.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye me-1"></i>View
                                                            </a>
                                                            <span class="text-muted">
                                                                <i class="fas fa-user me-1"></i>Requester: <?php echo htmlspecialchars($request['requester_name']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- My Requests -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">My Requests</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($assignments)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>You haven't accepted any requests yet.
                                    </div>
                                <?php else: ?>
                                    <div class="row g-4">
                                        <?php foreach ($assignments as $request): ?>
                                            <div class="col-12">
                                                <div class="card request-card">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($request['title']); ?></h5>
                                                        <p class="card-text text-muted">
                                                            <?php echo htmlspecialchars(substr($request['description'], 0, 100)) . '...'; ?>
                                                        </p>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="badge bg-<?php 
                                                                echo $request['status'] === 'completed' ? 'success' : 
                                                                    ($request['status'] === 'pending' ? 'warning' : 'danger'); 
                                                            ?>">
                                                                <?php echo ucfirst($request['status']); ?>
                                                            </span>
                                                            <small class="text-muted">
                                                                <?php echo date('M d, Y', strtotime($request['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                        <hr>
                                                        <div class="d-flex justify-content-between">
                                                            <a href="view_request.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye me-1"></i>View
                                                            </a>
                                                            <span class="text-muted">
                                                                <i class="fas fa-user me-1"></i>Requester: <?php echo htmlspecialchars($request['requester_name']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
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