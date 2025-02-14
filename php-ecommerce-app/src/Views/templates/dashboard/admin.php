<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Agora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="##site##">
                ##if:business_logo##
                <img src="##site####business_logo##" alt="Business Logo" class="me-2" style="height: 30px;">
                ##endif##
                Welcome Agora Admin: ##admin_name##
            </a>
            <div class="navbar-nav ms-auto">
                <button class="nav-link btn btn-link" data-bs-toggle="modal" data-bs-target="#profileModal">
                    <i class="bi bi-person-circle"></i> Edit Profile
                </button>
                <a class="nav-link" href="##site##">
                    <i class="bi bi-house-door"></i> Home
                </a>
                <a class="nav-link" href="##site##/logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        ##if:error##
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ##error##
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        ##endif##

        ##if:success##
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ##success##
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        ##endif##

        ##feedback##

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="bi bi-people"></i> Sellers
                        </h5>
                        <div class="row">
                            <div class="col">
                                <p class="card-text h3">##total_sellers##</p>
                                <p class="text-muted">Total Sellers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-box-seam"></i> Products
                        </h5>
                        <div class="row">
                            <div class="col">
                                <p class="card-text h3">##total_products##</p>
                                <p class="text-muted">Total Products</p>
                            </div>
                            <div class="col">
                                <p class="card-text h3">##active_products##</p>
                                <p class="text-muted">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title text-info">
                            <i class="bi bi-graph-up"></i> Business
                        </h5>
                        <div class="row">
                            <div class="col">
                                <p class="card-text h3">##total_orders##</p>
                                <p class="text-muted">Orders</p>
                            </div>
                            <div class="col">
                                <p class="card-text h3">$##total_revenue##</p>
                                <p class="text-muted">Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Business Logo Section -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Business Logo</h5>
                    </div>
                    <!-- Business Logo Section -->
                    <div class="card-body">
                        <div class="text-center mb-3">
                            ##if:has_logo##
                                <img src="##site####business_logo##" alt="Business Logo" class="img-fluid mb-2" style="max-height: 150px;">
                                <div class="text-muted small mb-2">Current Logo</div>
                            ##else##
                                <div class="border rounded p-3 mb-2">
                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    <div class="text-muted">No logo uploaded</div>
                                </div>
                            ##endif##
                        </div>
                        <form method="post" enctype="multipart/form-data" action="##site##/admin/dashboard">
                            <input type="hidden" name="action" value="upload_logo">
                            <div class="mb-3">
                                <label class="form-label">Choose new logo</label>
                                <input type="file" name="logo" class="form-control" accept="image/*" required>
                                <div class="form-text">Recommended size: 400x400px. Max file size: 2MB</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                ##if:has_logo##Update##else##Upload##endif## Logo
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add User Section -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Add New User</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="##site##/admin/dashboard">
                            <input type="hidden" name="action" value="add_user">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name*</label>
                                        <input type="text" name="user_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email*</label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Role*</label>
                                        <select name="role" class="form-select" required>
                                            <option value="Seller">Seller</option>
                                            <option value="Buyer">Buyer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password*</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" name="phone" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Add User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Sellers Section -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Manage Sellers</h5>
                    </div>
                    <div class="card-body">
                        ##sellers##
                    </div>
                </div>
            </div>

            <!-- Recent Orders Section -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        ##recentOrders##
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seller Details Modal -->
    <div class="modal fade" id="sellerDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seller Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Product Statistics</h6>
                            <div id="sellerProductStats">
                                Loading...
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Recent Activity</h6>
                            <div id="sellerActivity">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="##site##/admin/dashboard">
                <input type="hidden" name="action" value="update_profile">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name*</label>
                        <input type="text" name="user_name" class="form-control" required value="##admin_name##">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email*</label>
                        <input type="email" name="email" class="form-control" required value="##admin_email##">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" value="##admin_phone##">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3">##admin_address##</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="3">##admin_bio##</textarea>
                        <div class="form-text">Tell others about yourself and your role.</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Charts Section -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Function to view seller details
        function viewSellerDetails(sellerId) {
            // Clear previous data
            document.getElementById('sellerProductStats').innerHTML = 'Loading...';
            document.getElementById('sellerActivity').innerHTML = 'Loading...';

            // Show modal
            new bootstrap.Modal(document.getElementById('sellerDetailsModal')).show();

            // Fetch seller details
            fetch(`##site##/admin/api/seller-details?seller_id=${sellerId}`)
                .then(response => response.json())
                .then(data => {
                    updateSellerDetails(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('sellerProductStats').innerHTML = 'Error loading data';
                    document.getElementById('sellerActivity').innerHTML = 'Error loading data';
                });
        }

        // Function to update seller details in modal
        function updateSellerDetails(data) {
            // Update Product Stats
            const productStats = `
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Active Products
                        <span class="badge bg-primary rounded-pill">${data.activeProducts}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Total Sales
                        <span class="badge bg-success rounded-pill">$${data.totalSales}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Average Rating
                        <span class="badge bg-info rounded-pill">${data.averageRating}/5</span>
                    </div>
                </div>`;
            document.getElementById('sellerProductStats').innerHTML = productStats;

            // Update Recent Activity
            let activityHtml = '<div class="timeline">';
            data.recentActivity.forEach(activity => {
                activityHtml += `
                    <div class="timeline-item mb-3">
                        <small class="text-muted">${activity.date}</small>
                        <p class="mb-1">${activity.description}</p>
                    </div>`;
            });
            activityHtml += '</div>';
            document.getElementById('sellerActivity').innerHTML = activityHtml;
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialize any charts if needed
        document.addEventListener('DOMContentLoaded', function () {
            // Example: Add charts initialization here if required
        });

        // Handle seller status toggle confirmation
        document.querySelectorAll('form[action*="/admin/dashboard"]').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (this.querySelector('button').textContent.trim() === 'Deactivate') {
                    if (!confirm('Are you sure you want to deactivate this seller? This will hide their products from the marketplace.')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>

    <style>
        .timeline {
            position: relative;
            padding: 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 1.5rem;
            border-left: 2px solid #e9ecef;
        }

        .timeline-item:last-child {
            border-left-color: transparent;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .table> :not(caption)>*>* {
            padding: 1rem 0.75rem;
        }

        .badge {
            padding: 0.5em 0.8em;
        }

        /* Status badge styles */
        .badge.bg-success {
            background-color: #198754 !important;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #000;
        }

        .badge.bg-info {
            background-color: #0dcaf0 !important;
        }

        /* Modal improvements */
        .modal-header {
            border-bottom: 2px solid #f8f9fa;
        }

        .modal-footer {
            border-top: 2px solid #f8f9fa;
        }

        /* Table responsive improvements */
        .table-responsive {
            margin: 0;
            padding: 0;
            border-radius: 0.25rem;
        }

        /* Card shadow and border improvements */
        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .border-0 {
            border: none !important;
        }
    </style>
</body>

</html>