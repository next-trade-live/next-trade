<?php
// Simple admin panel for viewing contact submissions
session_start();

// Simple authentication (in production, use proper authentication)
$admin_username = 'admin';
$admin_password = 'hexagon2024'; // Change this password!

if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = 'Invalid credentials';
        }
    }
    
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login - Hexagon Trading</title>
            <style>
                body { font-family: Arial, sans-serif; background: #0f172a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                .login-form { background: #1e293b; padding: 2rem; border-radius: 8px; width: 300px; }
                .form-group { margin-bottom: 1rem; }
                label { display: block; margin-bottom: 0.5rem; }
                input { width: 100%; padding: 0.5rem; border: 1px solid #475569; background: #334155; color: white; border-radius: 4px; }
                button { width: 100%; padding: 0.75rem; background: #0891b2; color: white; border: none; border-radius: 4px; cursor: pointer; }
                button:hover { background: #0e7490; }
                .error { color: #ef4444; margin-bottom: 1rem; }
            </style>
        </head>
        <body>
            <form method="POST" class="login-form">
                <h2>Admin Login</h2>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </body>
        </html>
        <?php
        exit;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Database connection
$db_host = 'localhost';
$db_name = 'hexagon_trading';
$db_user = 'your_username';
$db_pass = 'your_password';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle status updates
if (isset($_POST['update_status'])) {
    $id = (int)$_POST['submission_id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    
    $stmt = $pdo->prepare("UPDATE contact_submissions SET status = ?, notes = ? WHERE id = ?");
    $stmt->execute([$status, $notes, $id]);
    
    $success_message = "Submission updated successfully!";
}

// Get all contact submissions
$stmt = $pdo->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC");
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_submissions,
        COUNT(CASE WHEN status = 'new' THEN 1 END) as new_submissions,
        COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted,
        COUNT(CASE WHEN status = 'enrolled' THEN 1 END) as enrolled
    FROM contact_submissions
");
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Hexagon Trading Institute</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0f172a; color: white; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .header h1 { color: #0891b2; }
        .logout-btn { background: #ef4444; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: #1e293b; padding: 1rem; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 2rem; font-weight: bold; color: #0891b2; }
        .submissions-table { width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px; overflow: hidden; }
        .submissions-table th, .submissions-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #475569; }
        .submissions-table th { background: #334155; }
        .status-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem; }
        .status-new { background: #fbbf24; color: #000; }
        .status-contacted { background: #3b82f6; }
        .status-enrolled { background: #10b981; }
        .status-closed { background: #6b7280; }
        .update-form { display: none; background: #334155; padding: 1rem; margin-top: 0.5rem; border-radius: 4px; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; }
        .form-group select, .form-group textarea { width: 100%; padding: 0.5rem; border: 1px solid #475569; background: #1e293b; color: white; border-radius: 4px; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; margin-right: 0.5rem; }
        .btn-primary { background: #0891b2; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .success-message { background: #10b981; color: white; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hexagon Trading Institute - Admin Panel</h1>
        <a href="?logout=1" class="logout-btn">Logout</a>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_submissions']; ?></div>
            <div>Total Submissions</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['new_submissions']; ?></div>
            <div>New</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['contacted']; ?></div>
            <div>Contacted</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['enrolled']; ?></div>
            <div>Enrolled</div>
        </div>
    </div>

    <table class="submissions-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course Interest</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td><?php echo $submission['id']; ?></td>
                    <td><?php echo htmlspecialchars($submission['name']); ?></td>
                    <td><?php echo htmlspecialchars($submission['email']); ?></td>
                    <td><?php echo htmlspecialchars($submission['course_interest']); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $submission['status']; ?>">
                            <?php echo ucfirst($submission['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M j, Y H:i', strtotime($submission['submitted_at'])); ?></td>
                    <td>
                        <button class="btn btn-primary" onclick="toggleUpdateForm(<?php echo $submission['id']; ?>)">
                            Update
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <div id="update-form-<?php echo $submission['id']; ?>" class="update-form">
                            <form method="POST">
                                <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                                
                                <div class="form-group">
                                    <label>Message:</label>
                                    <p style="background: #1e293b; padding: 0.5rem; border-radius: 4px;">
                                        <?php echo nl2br(htmlspecialchars($submission['message'])); ?>
                                    </p>
                                </div>
                                
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select name="status">
                                        <option value="new" <?php echo $submission['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="contacted" <?php echo $submission['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                        <option value="enrolled" <?php echo $submission['status'] === 'enrolled' ? 'selected' : ''; ?>>Enrolled</option>
                                        <option value="closed" <?php echo $submission['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Notes:</label>
                                    <textarea name="notes" rows="3"><?php echo htmlspecialchars($submission['notes']); ?></textarea>
                                </div>
                                
                                <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-secondary" onclick="toggleUpdateForm(<?php echo $submission['id']; ?>)">Cancel</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function toggleUpdateForm(id) {
            const form = document.getElementById('update-form-' + id);
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }
    </script>
</body>
</html>
