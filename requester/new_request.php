<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Only allow requesters
requireRequester();

$errors = array();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $location = sanitizeInput($_POST['location']);
    $urgency = isset($_POST['urgency']) ? sanitizeInput($_POST['urgency']) : '';

    // Validation
    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (empty($location)) $errors[] = 'Location is required.';
    if (!in_array($urgency, array('low', 'medium', 'high'))) $errors[] = 'Please select a valid urgency.';

    if (empty($errors)) {
        $data = array(
            'user_id' => (int)$_SESSION['user_id'],
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'urgency' => $urgency,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        );
        if (insertRecord('requests', $data)) {
            header('Location: dashboard.php?success=1');
            exit();
        } else {
            $errors[] = 'Failed to post request. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Request - HelpTap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f8fb; }
        .form-container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); padding: 2.5rem; }
        .form-title { font-weight: 700; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container mt-5">
            <h2 class="form-title"><i class="fas fa-plus-circle me-2 text-primary"></i>Post a New Help Request</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"> <?php echo htmlspecialchars($success); ?> </div>
            <?php endif; ?>
            <form method="POST" action="" autocomplete="off">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required maxlength="255">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required maxlength="255">
                </div>
                <div class="mb-3">
                    <label for="urgency" class="form-label">Urgency</label>
                    <select class="form-select" id="urgency" name="urgency" required>
                        <option value="">Select urgency</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i>Submit Request
                </button>
                <a href="dashboard.php" class="btn btn-link w-100 mt-2">&larr; Back to Dashboard</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 