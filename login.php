<?php
session_start();

// If already logged in, redirect
if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}

require_once 'config/Database.php';
require_once 'classes/User.php';

$error = "";

// Handle login form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Basic server-side validation
    if ($email === '' || $password === '') {
        $error = "Please fill in all fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6 || strlen($password) > 255) {
        $error = "Please enter a valid password.";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);

        if($user->login($email, $password)) {
            // Set session variables
            $_SESSION['user_id'] = $user->id;
            $_SESSION['name'] = $user->name;
            $_SESSION['email'] = $user->email;
            $_SESSION['role'] = $user->role;
            $_SESSION['photo'] = $user->photo;

            // Redirect based on role
            if($user->role == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BloodLink Manager</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.3);
        }
        .demo-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
        .demo-credentials small {
            display: block;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card mx-auto">
            <div class="login-header">
                <i class="fas fa-droplet"></i>
                <h2 class="mb-1">BloodLink Manager</h2>
                <p class="mb-0">Sign in to continue</p>
            </div>
            
            <div class="login-body">
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="admin@bloodlink.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn btn-danger btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>

                <div class="demo-credentials">
                    <div class="text-center mb-2">
                        <strong><i class="fas fa-info-circle me-2"></i>Demo Credentials</strong>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small><strong>Admin:</strong></small>
                            <small>admin@bloodlink.com</small>
                            <small>password123</small>
                        </div>
                        <div class="col-6">
                            <small><strong>User:</strong></small>
                            <small>john@bloodlink.com</small>
                            <small>password123</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>