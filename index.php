<?php
/**
 * BloodLink - Blood Donation Management System
 * Entry Point / Landing Page
 */
session_start();

// If user is already logged in, redirect to appropriate dashboard
if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BloodLink - Blood Donation Management System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255,255,255,0.95);
            margin-bottom: 2rem;
        }
        
        .droplet-icon {
            font-size: 6rem;
            color: white;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .btn-hero {
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        /* Features Section */
        .features-section {
            padding: 80px 0;
            background-color: #f8f9fa;
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border: none;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #1f2937;
        }
        
        .feature-description {
            color: #6b7280;
            line-height: 1.6;
        }
        
        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            padding: 60px 0;
            color: white;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: #ef4444;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.9);
        }
        
        /* Footer */
        .footer {
            background-color: #1f2937;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark position-absolute w-100" style="z-index: 1000;">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">
                <i class="fas fa-droplet me-2"></i>BloodLink
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <a href="login.php" class="btn btn-light">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <div class="droplet-icon">
                        <i class="fas fa-droplet"></i>
                    </div>
                    <h1 class="hero-title">Save Lives<br>Donate Blood</h1>
                    <p class="hero-subtitle">
                        Modern blood donation management system for organizations and donors
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="login.php" class="btn btn-light btn-hero">
                            <i class="fas fa-sign-in-alt me-2"></i>Get Started
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-hero">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block">
                    <i class="fas fa-heart-pulse" style="font-size: 20rem; color: rgba(255,255,255,0.2);"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold mb-3">Why Choose BloodLink?</h2>
                <p class="lead text-muted">Complete management solution for blood donation organizations</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="feature-title">Role-Based Access</h3>
                        <p class="feature-description">
                            Separate dashboards for administrators and donors with appropriate access controls
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <h3 class="feature-title">User Management</h3>
                        <p class="feature-description">
                            Complete CRUD operations for managing donor profiles and information
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h3 class="feature-title">Photo Profiles</h3>
                        <p class="feature-description">
                            Upload and manage profile photos for better donor identification
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="feature-title">Donation Tracking</h3>
                        <p class="feature-description">
                            Track donation history and eligibility for next donation automatically
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="feature-title">Quick Search</h3>
                        <p class="feature-description">
                            Find donors instantly by name, email, or blood type
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Secure & Safe</h3>
                        <p class="feature-description">
                            Built with security best practices including password hashing and SQL injection prevention
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-droplet"></i>
                        </div>
                        <div class="stat-label">8 Blood Types</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4 mb-md-0">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-label">Multiple Users</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-label">Secure System</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="stat-label">Fast & Easy</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Ready to Get Started?</h2>
            <p class="lead text-muted mb-4">Login to access your dashboard and start managing blood donations</p>
            <a href="login.php" class="btn btn-danger btn-lg px-5 py-3">
                <i class="fas fa-sign-in-alt me-2"></i>Login Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">
                <i class="fas fa-droplet me-2"></i>
                <strong>BloodLink</strong> - Blood Donation Management System © 2024
            </p>
            <p class="mt-2 mb-0 text-muted">
                <small>Built with PHP, MySQL, Bootstrap & jQuery</small>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        $(document).ready(function() {
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this.getAttribute('href'));
                if(target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            });
        });
    </script>
</body>
</html>