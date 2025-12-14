<?php
session_start();

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config/Database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$success = "";
$error = "";

// Get user ID from URL
$user_id = $_GET['id'] ?? 0;
$user->id = $user_id;

if(!$user->readOne()) {
    $_SESSION['error'] = "User not found!";
    header("Location: admin_dashboard.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if(empty($new_password)) {
        $error = "Password cannot be empty!";
    } elseif(strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif($new_password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        if($user->updatePassword($new_password)) {
            $_SESSION['success'] = "Password updated successfully for " . $user->name;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Failed to update password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password - BloodLink</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="./assets/css/update_password.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">
                <i class="fas fa-droplet me-2"></i>BloodLink
            </a>
            <div class="text-white">
                <strong><?php echo $_SESSION['name']; ?></strong>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header bg-yellow text-dark">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i>Update Password</h5>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="user-info">
                            <div class="d-flex align-items-center">
                                <img src="uploads/<?php echo $user->photo; ?>" 
                                     class="rounded-circle me-3" 
                                     width="60" height="60" 
                                     onerror="this.src='uploads/default-avatar.jpg'">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($user->name); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($user->email); ?></small>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-lock me-2"></i>New Password *</label>
                                <input type="password" class="form-control" name="new_password" 
                                       placeholder="Enter new password" required minlength="6">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-lock me-2"></i>Confirm Password *</label>
                                <input type="password" class="form-control" name="confirm_password" 
                                       placeholder="Confirm new password" required>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn update_password_btn">
                                    <i class="fas fa-save me-2"></i>Update Password
                                </button>
                                <a href="admin_dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>