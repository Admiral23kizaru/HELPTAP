<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

requireHelper();

// Get request ID
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch request details (join with user info)
$query = "SELECT r.*, u.name as requester_name, u.email as requester_email FROM requests r JOIN users u ON r.user_id = u.id WHERE r.id = $request_id";
$request = fetch_row($query);

if (!$request) {
    die('<div class="alert alert-danger m-4">Request not found.</div>');
}

// Check if helper has already accepted this request
$query = "SELECT * FROM assignments WHERE request_id = $request_id AND helper_id = " . (int)$_SESSION['user_id'];
$assignment = fetch_row($query);

// Handle Accept/Decline/Complete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept'])) {
        // Accept the request
        $query = "INSERT INTO assignments (request_id, helper_id, status, accepted_at) VALUES ($request_id, " . (int)$_SESSION['user_id'] . ", 'accepted', NOW())";
        if (run_query($query)) {
            $query = "UPDATE requests SET status = 'helping', helper_id = " . (int)$_SESSION['user_id'] . " WHERE id = $request_id";
            run_query($query);
            logActivity($_SESSION['user_id'], 'accept_request', "Accepted request #$request_id");
            header("Location: view_request.php?id=$request_id");
            exit();
        }
    } elseif (isset($_POST['decline'])) {
        // Decline: just redirect
        header('Location: dashboard.php');
        exit();
    } elseif (isset($_POST['complete'])) {
        // Complete the request
        $query = "UPDATE assignments SET status = 'completed', completed_at = NOW() WHERE request_id = $request_id AND helper_id = " . (int)$_SESSION['user_id'];
        run_query($query);
        $query = "UPDATE requests SET status = 'completed' WHERE id = $request_id";
        run_query($query);
        logActivity($_SESSION['user_id'], 'complete_request', "Completed request #$request_id");
        header("Location: view_request.php?id=$request_id");
        exit();
    }
}

// Fetch updated assignment after any action
$query = "SELECT * FROM assignments WHERE request_id = $request_id AND helper_id = " . (int)$_SESSION['user_id'];
$assignment = fetch_row($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Request - HelpTap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f8fb; }
        .container { max-width: 700px; margin: 40px auto; }
        .card { border-radius: 1rem; }
    </style>
</head>
<body>
<div class="container mt-5">
    <a href="dashboard.php" class="btn btn-link mb-3">&larr; Back to Dashboard</a>
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title mb-3"><?php echo htmlspecialchars($request['title']); ?></h3>
            <p class="mb-2"><strong>Description:</strong> <?php echo htmlspecialchars($request['description']); ?></p>
            <p class="mb-2"><strong>Location:</strong> <?php echo htmlspecialchars($request['location']); ?></p>
            <p class="mb-2"><strong>Urgency:</strong> <span class="badge bg-info text-dark"><?php echo htmlspecialchars(ucfirst($request['urgency'])); ?></span></p>
            <p class="mb-2"><strong>Requester:</strong> <?php echo htmlspecialchars($request['requester_name']); ?> (<?php echo htmlspecialchars($request['requester_email']); ?>)</p>
            <p class="mb-2"><strong>Status:</strong> <span class="badge bg-<?php echo $request['status'] === 'completed' ? 'success' : ($request['status'] === 'helping' ? 'primary' : 'warning'); ?>"><?php echo htmlspecialchars(ucfirst($request['status'])); ?></span></p>
            <hr>
            <?php if (!$assignment): ?>
                <form method="POST">
                    <button type="submit" name="accept" class="btn btn-success me-2"><i class="fas fa-check"></i> Accept</button>
                    <button type="submit" name="decline" class="btn btn-danger"><i class="fas fa-times"></i> Decline</button>
                </form>
            <?php elseif ($assignment['status'] === 'accepted' && $request['status'] !== 'completed'): ?>
                <form method="POST">
                    <button type="submit" name="complete" class="btn btn-primary"><i class="fas fa-check-circle"></i> Mark as Complete</button>
                </form>
            <?php elseif ($assignment['status'] === 'completed'): ?>
                <div class="alert alert-success">You have completed this request.</div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Feedback placeholder for future implementation -->
    <?php if ($assignment && $assignment['status'] === 'completed'): ?>
        <div class="card mt-4">
            <div class="card-body">
                <h5>Feedback from Requester (coming soon)</h5>
                <p class="text-muted">The requester will be able to leave feedback here after completion.</p>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 