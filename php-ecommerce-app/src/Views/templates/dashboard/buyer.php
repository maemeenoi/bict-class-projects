<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard - Agora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .product-card {
            height: 100%;
        }

        .product-card .card-text {
            height: 4.5rem;
            overflow: hidden;
        }

        .badge {
            font-size: 0.875rem;
            padding: 0.5em 0.8em;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="##site##">Welcome Agora Buyer: ##buyer_name##</a>
            <div class="navbar-nav ms-auto">
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

        <!-- Profile Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Welcome, ##buyer_name##</h5>
                        <p class="card-text">
                            <small class="text-muted">Connected to: ##business_name##</small><br>
                            <small class="text-muted">Location: ##location_name##</small>
                        </p>
                        <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#profileModal">
                                <i class="bi bi-person"></i> Edit Profile
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#passwordModal">
                                <i class="bi bi-key"></i> Change Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Products -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="card-title mb-0">Available Products</h5>
                <div class="input-group w-auto">
                    <input type="text" class="form-control" placeholder="Search products..." id="productSearch">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Sort By
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" onclick="sortProducts('price-asc')">Price: Low to High</a>
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="sortProducts('price-desc')">Price: High to
                                Low</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortProducts('newest')">Newest First</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4" id="productsContainer">
                    ##products##
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                ##recent_orders##
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/buyer/dashboard">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name*</label>
                            <input type="text" name="user_name" class="form-control" required value="##buyer_name##">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email*</label>
                            <input type="email" name="email" class="form-control" required value="##email##">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="##phone##">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3">##address##</textarea>
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

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/buyer/dashboard" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="change_password">

                    <div class="modal-header">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Current Password*</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password*</label>
                            <input type="password" name="new_password" class="form-control" required minlength="8">
                            <div class="form-text">Password must be at least 8 characters long.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password*</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="8">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Purchase Modal -->
    <div class="modal fade" id="purchaseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/buyer/dashboard" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="buy_now">
                    <input type="hidden" name="product_id" id="purchase_product_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Purchase</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="purchase_product_details" class="mb-4"></div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="purchase_quantity" class="form-control" required
                                min="1" value="1" onchange="updateTotal()">
                            <div class="form-text">Available stock: <span id="available_stock">0</span></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Total Amount</label>
                            <div class="form-control" id="total_amount">$0.00</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Shipping Address</label>
                            <textarea name="shipping_address" class="form-control" required
                                rows="3">##address##</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm Purchase</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentProduct = null;

        function showPurchaseModal(product) {
            currentProduct = product;
            document.getElementById('purchase_product_id').value = product.product_id;
            document.getElementById('available_stock').textContent = product.stock_quantity;
            document.getElementById('purchase_quantity').max = product.stock_quantity;
            document.addEventListener('DOMContentLoaded', function () {
                var purchaseForm = document.querySelector('#purchaseModal form');
                if (purchaseForm) {
                    purchaseForm.addEventListener('submit', function () {
                        // Hide modal after form submission
                        var purchaseModal = bootstrap.Modal.getInstance(document.getElementById('purchaseModal'));
                        if (purchaseModal) {
                            purchaseModal.hide();
                        }
                    });
                }
            });

            const details = `
                <h6>${product.product_name}</h6>
                <p class="mb-1">${product.description || 'No description available.'}</p>
                <p class="mb-1"><strong>Price:</strong> $${parseFloat(product.price).toFixed(2)}</p>
                <p class="mb-0"><strong>Seller:</strong> ${product.seller_name}</p>
            `;
            document.getElementById('purchase_product_details').innerHTML = details;
            updateTotal();

            new bootstrap.Modal(document.getElementById('purchaseModal')).show();
            document.getElementById('productSearch').addEventListener('keyup', function (e) {
                const searchText = e.target.value.toLowerCase();
                const products = document.getElementsByClassName('product-card');

                Array.from(products).forEach(product => {
                    const title = product.querySelector('.card-title').textContent.toLowerCase();
                    const description = product.querySelector('.card-text').textContent.toLowerCase();

                    if (title.includes(searchText) || description.includes(searchText)) {
                        product.style.display = '';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        }

        function updateTotal() {
            if (!currentProduct) return;

            const quantity = parseInt(document.getElementById('purchase_quantity').value) || 0;
            const total = quantity * parseFloat(currentProduct.price);
            document.getElementById('total_amount').textContent = `$${total.toFixed(2)}`;
        }

        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })();

        function sortProducts(method) {
            const container = document.getElementById('productsContainer');
            const products = Array.from(container.getElementsByClassName('product-card'));

            products.sort((a, b) => {
                switch (method) {
                    case 'price-asc':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price-desc':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'newest':
                        return new Date(b.dataset.date) - new Date(a.dataset.date);
                }
            });

            products.forEach(product => container.appendChild(product));
        }
    </script>
</body>

</html>