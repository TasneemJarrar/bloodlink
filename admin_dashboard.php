<?php
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'config/Database.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Handle search
$search_keyword = $_GET['search'] ?? '';
if(!empty($search_keyword)) {
    $stmt = $user->search($search_keyword);
} else {
    $stmt = $user->readAll();
}

$users = $stmt->fetchAll();

// Get session messages
$success_msg = $_SESSION['success'] ?? '';
$error_msg = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BloodLink</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }
        .main-content {
            padding: 30px 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            border-radius: 15px 15px 0 0 !important;
        }
        .table-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .badge-admin {
            background-color: rgba(138, 92, 246, 0.22);
            color: #7e51e8ff;
            border-radius: 24px;
        }
        .badge-user {
            background-color: rgba(16, 185, 129, 0.22);
            color : #078d60ff;
            border-radius: 24px;
        }
        .blood-type-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #fee2e2;
            color: #dc2626;
            font-weight: bold;
            font-size: 0.8rem;
        }
        .btn_edit i{
            color: #4f46e5;
        }
        .btn_edit:hover{
            background: rgba(78, 70, 229, 0.18);
        }
        .update_password_btn i{
          color: #f59e0b;
        }
        .update_password_btn:hover{
          background: rgba(245, 159, 11, 0.18);
        }
        .delete_user_btn i{
          color: #f50b0bff;
        }
        .delete_user_btn:hover{
          background: rgba(245, 11, 11, 0.18);
        }
        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">
                <i class="fas fa-droplet me-2"></i>BloodLink
            </a>
            
            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    <i class="fas fa-shield-alt me-1"></i>
                    <strong><?php echo $_SESSION['name']; ?></strong>
                </span>
                <img src="uploads/<?php echo $_SESSION['photo']; ?>" class="user-avatar me-3" alt="Profile" onerror="this.src='uploads/default-avatar.jpg'">
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid main-content">
        <div class="container">
            <!-- Session Messages -->
            <?php if(!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
                    <p class="text-muted">Manage donors, update records, and system users</p>
                </div>
                <a href="add_user.php" class="btn btn-danger">
                    <i class="fas fa-plus me-2"></i>Add User
                </a>
            </div>

            <!-- Users Table Card -->
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>All Users</h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="text" class="form-control me-2" name="search" 
                                       placeholder="Search by name or email..." 
                                       value="<?php echo htmlspecialchars($search_keyword); ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <?php if(!empty($search_keyword)): ?>
                                    <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Clear</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Blood Type</th>
                                    <th>Age</th>
                                    <th>Last Donation</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($users) > 0): ?>
                                    <?php foreach($users as $u): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="uploads/<?php echo $u['photo']; ?>" 
                                                         class="table-avatar me-3" 
                                                         alt="<?php echo $u['name']; ?>"
                                                         onerror="this.src='uploads/default-avatar.jpg'">
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($u['name']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $u['role'] == 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                                    <?php echo ucfirst($u['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="blood-type-badge"><?php echo $u['blood_type']; ?></span>
                                            </td>
                                            <td><?php echo $u['age']; ?> years</td>
                                            <td>
                                                <?php if($u['last_donation_date']): ?>
                                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                                    <?php echo date('M d, Y', strtotime($u['last_donation_date'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No record</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="edit_user.php?id=<?php echo $u['id']; ?>" 
                                                   class="btn btn-sm btn-action btn_edit" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="update_password.php?id=<?php echo $u['id']; ?>" 
                                                   class="btn btn-sm update_password_btn btn-action" 
                                                   title="Change Password">
                                                    <i class="fas fa-key"></i>
                                                </a>
                                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                                    <a href="delete_user.php?id=<?php echo $u['id']; ?>" 
                                                       class="btn btn-sm delete_user_btn btn-action" 
                                                       title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                            No users found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>