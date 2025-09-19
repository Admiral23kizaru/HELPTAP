<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Require admin role
requireAdmin();

$success = '';
$error = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($current_password)) {
        $error = 'Current password is required.';
    } elseif (empty($new_password)) {
        $error = 'New password is required.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } else {
        // Verify current password
        $query = "SELECT password FROM users WHERE id = " . (int)$_SESSION['user_id'];
        $user = get_row($query);
        
        if ($user && password_verify($current_password, $user['password'])) {
            // Update password
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $data = array('password' => $hashed);
            $where = "id = " . (int)$_SESSION['user_id'];
            
            if (updateRecord('users', $data, $where)) {
                // Log password change
                logActivity($_SESSION['user_id'], 'password_change', 'Admin changed password');
                $success = 'Password updated successfully.';
            } else {
                $error = 'Failed to update password. Please try again.';
            }
        } else {
            $error = 'Current password is incorrect.';
        }
    }
}

$query = "SELECT * FROM users WHERE id = " . (int)$_SESSION['user_id'];
$admin = get_row($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - HelpTap</title>
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
                        <a class="nav-link active" href="profile.php">
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
                <div class="profile-header">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin['name']); ?>&background=random" 
                             alt="Profile" class="profile-avatar me-4">
                        <div>
                            <h2 class="mb-1"><?php echo htmlspecialchars($admin['name']); ?></h2>
                            <p class="mb-0"><?php echo htmlspecialchars($admin['email']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Change Password</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($success): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                                <?php endif; ?>
                                <?php if ($error): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>
                                <form method="POST" action="" id="passwordForm">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            <span class="input-group-text show-hide" onclick="togglePassword('current_password', 'eyeIcon1')">
                                                <i class="fa-solid fa-eye" id="eyeIcon1"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            <span class="input-group-text show-hide" onclick="togglePassword('new_password', 'eyeIcon2')">
                                                <i class="fa-solid fa-eye" id="eyeIcon2"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            <span class="input-group-text show-hide" onclick="togglePassword('confirm_password', 'eyeIcon3')">
                                                <i class="fa-solid fa-eye" id="eyeIcon3"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="updateBtn">
                                        <span id="updateText">Update Password</span>
                                        <span class="spinner-border spinner-border-sm" id="updateSpinner" role="status" aria-hidden="true" style="display: none;"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId, iconId) {
            var pwd = document.getElementById(fieldId);
            var icon = document.getElementById(iconId);
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        document.getElementById('passwordForm').addEventListener('submit', function() {
            document.getElementById('updateBtn').disabled = true;
            document.getElementById('updateText').textContent = 'Updating...';
            document.getElementById('updateSpinner').style.display = 'inline-block';
        });
    </script>
</body>
</html> 