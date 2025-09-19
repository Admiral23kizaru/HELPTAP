<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// If already logged in as admin, redirect to admin dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE email = '$email' AND role = 'admin'";
    $admin = fetch_row($query);
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['name'] = $admin['name'];
        $_SESSION['role'] = 'admin';
        
        // Log successful login
        logActivity($admin['id'], 'admin_login', 'Admin logged in successfully');
        
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - HelpTap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            min-height: 100vh;
        }
        .login-container { 
            max-width: 400px; 
            margin: 60px auto; 
            padding: 2.5rem 2rem 2rem 2rem; 
            background: rgba(255, 255, 255, 0.97);
            border-radius: 20px; 
            box-shadow: 0 8px 32px rgba(31,38,135,0.13);
            text-align: center;
        }
        .header-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1.2rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
        }
        .form-control:focus {
            box-shadow: 0 4px 12px rgba(0,0,0,0.10);
        }
        .btn-primary {
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .input-group-text {
            background: #f1f1f1;
            border-radius: 8px 0 0 8px;
        }
        .show-hide {
            cursor: pointer;
        }
        .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container mt-5">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=facearea&w=400&h=400&facepad=2" alt="HelpTap" class="header-img">
            <h2 class="mb-3"><i class="fa-solid fa-shield-halved me-2 text-primary"></i>Admin Login</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="" id="loginForm">
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="input-group-text show-hide" onclick="togglePassword()">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3" id="loginBtn">
                    <span id="loginText">Login</span>
                    <span class="spinner-border spinner-border-sm" id="loginSpinner" role="status" aria-hidden="true"></span>
                </button>
            </form>
            <div class="text-center">
                <a href="../index.php" class="text-decoration-none">&larr; Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            var pwd = document.getElementById('password');
            var icon = document.getElementById('eyeIcon');
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
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('loginBtn').disabled = true;
            document.getElementById('loginText').textContent = 'Logging in...';
            document.getElementById('loginSpinner').style.display = 'inline-block';
        });
    </script>
</body>
</html> 