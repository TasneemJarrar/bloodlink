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

$user_id = $_GET['id'] ?? 0;

// Prevent deleting yourself
if($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account!";
    header("Location: admin_dashboard.php");
    exit();
}

// Get user info before deletion (for photo cleanup)
$user->id = $user_id;
if($user->readOne()) {
    // Delete user
    if($user->delete()) {
        // Delete photo file if not default
        if($user->photo != 'default-avatar.jpg' && file_exists('uploads/' . $user->photo)) {
            unlink('uploads/' . $user->photo);
        }
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete user!";
    }
} else {
    $_SESSION['error'] = "User not found!";
}

header("Location: admin_dashboard.php");
exit();
?>