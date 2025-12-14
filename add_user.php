<?php
session_start();
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->name = $_POST['name'] ?? '';
    $user->email = $_POST['email'] ?? '';
    $user->password = $_POST['password'] ?? '';
    $user->role = $_POST['role'] ?? 'user';
    $user->blood_type = $_POST['blood_type'] ?? '';
    $user->age = $_POST['age'] ?? 0;
    $user->last_donation_date = $_POST['last_donation_date'] ?? null;
    
    $photo_name = 'default-avatar.jpg';
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $photo_name = time() . '_' . $filename;
            $upload_path = 'uploads/' . $photo_name;
            
            if(!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $error = "Failed to upload photo!";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed!";
        }
    }
    $user->photo = $photo_name;
    
    if(empty($error)) {
        if($user->emailExists($user->email)) {
            $error = "Email already exists!";
        } elseif($user->create()) {
            $success = "User created successfully!";
            // Clear form
            $_POST = array();
        } else {
            $error = "Failed to create user!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - BloodLink</title>
    
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
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .preview-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }
    </style>
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
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New User</h5>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="text-center mb-4">
                                <img src="uploads/default-avatar.jpg" id="photoPreview" class="preview-image mb-3" alt="Preview">
                                <div>
                                    <label for="photo" class="btn btn-outline-danger">
                                        <i class="fas fa-camera me-2"></i>Upload Photo
                                    </label>
                                    <input type="file" class="d-none" id="photo" name="photo" accept="image/*">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-user me-2"></i>Full Name *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-envelope me-2"></i>Email *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-lock me-2"></i>Password *</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-user-tag me-2"></i>Role *</label>
                                    <select class="form-select" name="role" required>
                                        <option value="user">Normal User</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-droplet me-2"></i>Blood Type *</label>
                                    <select class="form-select" name="blood_type" required>
                                        <option value="">Select Blood Type</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-birthday-cake me-2"></i>Age *</label>
                                    <input type="number" class="form-control" name="age" min="18" max="100" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-calendar-alt me-2"></i>Last Donation Date (Optional)</label>
                                <input type="date" class="form-control" name="last_donation_date">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-2"></i>Create User
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#photo').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#photoPreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>