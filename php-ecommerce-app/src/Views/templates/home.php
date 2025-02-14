<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Agora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .hero-section {
            background-color: #f8f9fa;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="##site##">
                <i class="bi bi-shop"></i> Agora
            </a>
            <div class="navbar-nav ms-auto">
                ##if:isLoggedIn##
                <a class="nav-link" href="##site##/##userRole##/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link" href="##site##/logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
                ##endif##
                ##if:!isLoggedIn##
                <a class="nav-link" href="##site##/login">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a class="nav-link" href="##site##/register">
                    <i class="bi bi-person-plus"></i> Register
                </a>
                ##endif##
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-center">
        <div class="container">
            <img src="/Agora_V.3/public/images/logo.png" alt="Logo" class="img-fluid mb-4" style="max-width: 200px;">
            <h1 class="display-4">Welcome to Agora</h1>
            <p class="lead">Your Local Online Marketplace</p>
            ##if:!isLoggedIn##
            <div class="mt-4">
                <a href="##site##/register" class="btn btn-primary btn-lg me-2">Join Now</a>
                <a href="##site##/login" class="btn btn-secondary btn-lg">Login</a>
            </div>
            ##endif##
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        ##feedback##

        <!-- Features Section -->
        <div class="row mt-5 text-center">
            <h2 class="mb-4">Why Choose Agora?</h2>
            <div class="col-md-4 mb-4">
                <i class="bi bi-shop display-4 text-primary"></i>
                <h4 class="mt-3">Local Businesses</h4>
                <p>Connect with trusted local sellers in your area</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="bi bi-shield-check display-4 text-primary"></i>
                <h4 class="mt-3">Secure Shopping</h4>
                <p>Safe and secure transactions guaranteed</p>
            </div>
            <div class="col-md-4 mb-4">
                <i class="bi bi-people display-4 text-primary"></i>
                <h4 class="mt-3">Community Driven</h4>
                <p>Support your local community and economy</p>
            </div>
        </div>

        <!-- Featured Products -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-4">Featured Products</h2>
                <div class="mb-4">
                    ##if:!isLoggedIn##
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Please <a href="##site##/login" class="alert-link">login</a> to view product details and make
                        purchases.
                    </div>
                    ##endif##
                </div>
                ##products##
            </div>
        </div>


    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 Agora. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>