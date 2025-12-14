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

$success = "";
$error = "";

// Get current user data
$user->id = $_SESSION['user_id'];
if(!$user->readOne()) {
    header("Location: logout.php");
    exit();
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user->name = $_POST['name'] ?? '';
    $user->email = $_POST['email'] ?? '';
    $user->blood_type = $_POST['blood_type'] ?? '';
    $user->age = $_POST['age'] ?? 0;
    $user->last_donation_date = $_POST['last_donation_date'] ?? null;
    
    // Handle file upload
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $photo_name = time() . '_' . $filename;
            $upload_path = 'uploads/' . $photo_name;
            
            if(move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                // Delete old photo if not default
                if($user->photo != 'default-avatar.jpg' && file_exists('uploads/' . $user->photo)) {
                    unlink('uploads/' . $user->photo);
                }
                $user->photo = $photo_name;
            }
        }
    }
    
    // Update user
    if($user->update()) {
        $success = "Profile updated successfully!";
        // Update session
        $_SESSION['name'] = $user->name;
        $_SESSION['photo'] = $user->photo;
    } else {
        $error = "Failed to update profile!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - BloodLink</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="./assets/css/edit_profile.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="user_dashboard.php">
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
                        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit My Profile</h5>
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
                            <!-- Photo Upload -->
                            <div class="text-center mb-4">
                                <img src="uploads/<?php echo $user->photo; ?>" id="photoPreview" class="preview-image mb-3" alt="Preview" onerror="this.src='uploads/default-avatar.jpg'">
                                <div>
                                    <label for="photo" class="btn btn-outline-danger">
                                        <i class="fas fa-camera me-2"></i>Change Photo
                                    </label>
                                    <input type="file" class="d-none" id="photo" name="photo" accept="image/*">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-user me-2"></i>Full Name *</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user->name); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-envelope me-2"></i>Email *</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user->email); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-droplet me-2"></i>Blood Type *</label>
                                    <select class="form-select" name="blood_type" required>
                                        <?php 
                                        $blood_types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                        foreach($blood_types as $type): 
                                        ?>
                                            <option value="<?php echo $type; ?>" <?php echo $user->blood_type == $type ? 'selected' : ''; ?>>
                                                <?php echo $type; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-birthday-cake me-2"></i>Age *</label>
                                    <input type="number" class="form-control" name="age" min="18" max="100" value="<?php echo $user->age; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-calendar-alt me-2"></i>Last Donation Date</label>
                                <input type="date" class="form-control" name="last_donation_date" value="<?php echo $user->last_donation_date; ?>">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <a href="user_dashboard.php" class="btn btn-secondary">
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
        // Preview image before upload
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