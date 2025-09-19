<?php
session_start();
require_once '../config/db.php';
require_once 'functions.php';

// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    header('Location: ' . getDashboardUrl());
    exit();
}

$errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? sanitizeInput($_POST['role']) : '';

    // Validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (empty($password)) $errors[] = 'Password is required.';
    elseif (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';
    if ($role !== 'requester' && $role !== 'helper') $errors[] = 'Please select a valid role.';

    // Check if email already exists
    $query = "SELECT id FROM users WHERE email = '$email'";
    $existing = fetch_row($query);
    if ($existing) $errors[] = 'Email already registered.';

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $data = array(
            'name' => $name,
            'email' => $email,
            'password' => $hashed,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        );
        
        if (insertRecord('users', $data)) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: login.php');
            exit();
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - HelpTap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            min-height: 100vh;
        }
        .signup-container { 
            max-width: 500px; 
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
        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }
        .role-option {
            flex: 1;
            padding: 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: #f8f9fa;
        }
        .role-option:hover, .role-option.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        .role-option input[type="radio"] {
            display: none;
        }
        .role-icon {
            font-size: 1.5rem;
            margin-bottom: 0.3rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-container mt-5">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=facearea&w=400&h=400&facepad=2" alt="HelpTap" class="header-img">
            <h2 class="mb-3"><i class="fa-solid fa-user-plus me-2 text-primary"></i>Create Your Account</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="POST" action="" id="signupForm">
                <div class="mb-3 text-start">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="input-group-text show-hide" onclick="togglePassword('password', 'eyeIcon1')">
                            <i class="fa-solid fa-eye" id="eyeIcon1"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <span class="input-group-text show-hide" onclick="togglePassword('confirm_password', 'eyeIcon2')">
                            <i class="fa-solid fa-eye" id="eyeIcon2"></i>
                        </span>
                    </div>
                </div>
                <div class="mb-4 text-start">
                    <label class="form-label d-block">Register as</label>
                    <div class="role-selector">
                        <label class="role-option" id="roleRequester">
                            <input type="radio" name="role" value="requester" required>
                            <i class="fas fa-user-plus role-icon mb-2"></i>
                            <div>Requester</div>
                            <small class="text-muted">Request help</small>
                        </label>
                        <label class="role-option" id="roleHelper">
                            <input type="radio" name="role" value="helper" required>
                            <i class="fas fa-hands-helping role-icon mb-2"></i>
                            <div>Helper</div>
                            <small class="text-muted">Offer help</small>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3" id="signupBtn">
                    <span id="signupText">Create Account</span>
                    <span class="spinner-border spinner-border-sm" id="signupSpinner" role="status" aria-hidden="true"></span>
                </button>
            </form>
            <div class="text-center">
                <p class="mb-2">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                <a href="../index.php" class="text-decoration-none">&larr; Back to Home</a>
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

        // Handle role selection
        var roleOptions = document.querySelectorAll('.role-option');
        roleOptions.forEach(function(option) {
            option.addEventListener('click', function() {
                roleOptions.forEach(function(opt) {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Handle form submission
        document.getElementById('signupForm').addEventListener('submit', function() {
            document.getElementById('signupBtn').disabled = true;
            document.getElementById('signupText').textContent = 'Creating Account...';
            document.getElementById('signupSpinner').style.display = 'inline-block';
        });
    </script>
</body>
</html> 