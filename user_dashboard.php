<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/Database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$user->id = $_SESSION['user_id'];
$user->readOne();

// Calculate eligibility (3 months from last donation)
$eligibility_status = "Eligible Now";
$eligibility_class = "success";
$days_remaining = 0;

if($user->last_donation_date) {
    $last_date = new DateTime($user->last_donation_date);
    $next_date = clone $last_date;
    $next_date->modify('+3 months');
    $today = new DateTime();
    
    if($today < $next_date) {
        $interval = $today->diff($next_date);
        $days_remaining = $interval->days;
        $eligibility_status = "Wait " . $days_remaining . " days";
        $eligibility_class = "warning";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - BloodLink</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="./assets/css/user_dashboard.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_dashboard.php">
                <i class="fas fa-droplet me-2"></i>BloodLink
            </a>
            
            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    <i class="fas fa-user me-1"></i>
                    <strong><?php echo $_SESSION['name']; ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="profile-header"></div>
            <div class="card-body text-center">
                <img src="uploads/<?php echo $user->photo; ?>" class="profile-avatar" alt="Profile" onerror="this.src='uploads/default-avatar.jpg'">
                <h2 class="mt-3 mb-1"><?php echo htmlspecialchars($user->name); ?></h2>
                <p class="text-muted mb-3">
                    <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($user->email); ?>
                </p>
                <a href="edit_profile.php" class="btn btn-danger">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
        </div>

        <!-- Info Cards Row -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="info-card text-center">
                    <i class="fas fa-droplet blood-type-display"></i>
                    <p class="text-muted text-uppercase mb-2">Blood Type</p>
                    <h3 class="blood-type-display"><?php echo $user->blood_type; ?></h3>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-card text-center">
                    <i class="fas fa-user text-primary"></i>
                    <p class="text-muted text-uppercase mb-2">Age</p>
                    <h3 class="text-dark"><?php echo $user->age; ?> <small class="text-muted" style="font-size: 1rem;">years</small></h3>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-card text-center">
                    <i class="fas fa-clock text-<?php echo $eligibility_class; ?>"></i>
                    <p class="text-muted text-uppercase mb-2">Next Donation</p>
                    <h3 class="text-<?php echo $eligibility_class; ?>" style="font-size: 1.5rem;">
                        <?php echo $eligibility_status; ?>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Donation History -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Donation History</h5>
            </div>
            <div class="card-body">
                <?php if($user->last_donation_date): ?>
                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6 class="mb-1">Whole Blood Donation</h6>
                            <small class="text-muted">City Hospital Center</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark mb-1">
                                <?php echo date('M d, Y', strtotime($user->last_donation_date)); ?>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                        <p>No donation history found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>