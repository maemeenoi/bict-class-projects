<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - Agora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="##site##">Welcome Agora Seller: ##seller##</a>
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

        ##feedback##

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            <i class="bi bi-box-seam"></i> Total Products
                        </h5>
                        <p class="card-text h3">##total_products##</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-primary">
                            <i class="bi bi-check-circle"></i> Active Products
                        </h5>
                        <p class="card-text h3">##active_products##</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-info">
                            <i class="bi bi-cart-check"></i> Total Orders
                        </h5>
                        <p class="card-text h3">##total_orders##</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            <i class="bi bi-graph-up"></i> Total Sales
                        </h5>
                        <p class="card-text h3">$##total_sales##</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" id="sellerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products"
                    type="button" role="tab">
                    <i class="bi bi-box-seam"></i> Products
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button"
                    role="tab">
                    <i class="bi bi-cart"></i> Orders
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
                    role="tab">
                    <i class="bi bi-person"></i> Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button"
                    role="tab">
                    <i class="bi bi-building"></i> Business Info
                </button>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content" id="sellerTabsContent">
            <!-- Products Tab -->
            <div class="tab-pane fade show active" id="products" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">My Products</h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="bi bi-plus-lg"></i> Add Product
                        </button>
                    </div>
                    <div class="card-body">
                        ##products##
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Manage Orders</h5>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                Filter by Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterOrders('all')">All Orders</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterOrders('pending')">Pending</a></li>
                                <li><a class="dropdown-item" href="#"
                                        onclick="filterOrders('processing')">Processing</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterOrders('shipped')">Shipped</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterOrders('delivered')">Delivered</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="filterOrders('cancelled')">Cancelled</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Product</th>
                                        <th>Buyer</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Order Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ##orders##
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Profile Tab -->
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">My Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="##site##/seller/dashboard">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="mb-3">
                                <label class="form-label">Full Name*</label>
                                <input type="text" name="user_name" class="form-control" value="##user_name##" required
                                    maxlength="50">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email*</label>
                                <input type="email" name="email" class="form-control" value="##email##" required
                                    maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" value="##phone##" maxlength="20">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"
                                    maxlength="255">##address##</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="3">##bio##</textarea>
                                <div class="form-text">Tell customers about yourself and your products.</div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>

                        <hr class="my-4">

                        <h5 class="mb-4">Change Password</h5>
                        <form method="post" action="##site##/seller/dashboard">
                            <input type="hidden" name="action" value="change_password">

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
                                <input type="password" name="confirm_password" class="form-control" required
                                    minlength="8">
                            </div>

                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Business Info Tab -->
            <div class="tab-pane fade" id="business" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Business Information</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Business Name</dt>
                            <dd class="col-sm-9">##business_name##</dd>

                            <dt class="col-sm-3">Location</dt>
                            <dd class="col-sm-9">##business_location##</dd>

                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">##business_address##</dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">##business_phone##</dd>

                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9">##business_email##</dd>

                            <dt class="col-sm-3">Hours</dt>
                            <dd class="col-sm-9">##business_hours##</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/seller/dashboard">
                    <input type="hidden" name="action" value="add">

                    <div class="modal-header">
                        <h5 class="modal-title">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name*</label>
                            <input type="text" name="product_name" class="form-control" required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="">Select Category</option>
                                ##categories##
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price*</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price" class="form-control" required min="0" step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock Quantity*</label>
                            <input type="number" name="stock_quantity" class="form-control" required min="0" value="0">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/seller/dashboard">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" id="edit_product_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name*</label>
                            <input type="text" name="product_name" id="edit_product_name" class="form-control" required
                                maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" id="edit_category" class="form-select">
                                <option value="">Select Category</option>
                                ##categories##
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price*</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price" id="edit_price" class="form-control" required min="0"
                                    step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock Quantity*</label>
                            <input type="number" name="stock_quantity" id="edit_stock_quantity" class="form-control"
                                required min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="available">Available</option>
                                <option value="out_of_stock">Out of Stock</option>
                                <option value="discontinued">Discontinued</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Order Status Modal -->
    <div class="modal fade" id="updateOrderStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/seller/dashboard">
                    <input type="hidden" name="action" value="update_order_status">
                    <input type="hidden" name="order_id" id="status_order_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Update Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">New Status</label>
                            <select name="status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Order Delete Modal -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/seller/dashboard">
                    <input type="hidden" name="action" value="delete_order">
                    <input type="hidden" name="order_id" id="delete_order_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Are you sure you want to delete this cancelled order? This action cannot be undone.</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="##site##/seller/dashboard">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="product_id" id="delete_product_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduct(product) {
            document.getElementById('edit_product_id').value = product.product_id;
            document.getElementById('edit_product_name').value = product.product_name;
            document.getElementById('edit_category').value = product.category || '';
            document.getElementById('edit_description').value = product.description || '';
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock_quantity').value = product.stock_quantity;
            document.getElementById('edit_status').value = product.status;
            document.addEventListener('DOMContentLoaded', function () {
                const tabList = document.querySelector('#sellerTabs');
                const ordersTab = document.createElement('li');
                ordersTab.className = 'nav-item';
                ordersTab.innerHTML = `
            <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                <i class="bi bi-cart"></i> Orders
            </button>
        `;
                tabList.appendChild(ordersTab);
            });
            document.addEventListener('DOMContentLoaded', function () {
                // Get active tab from URL hash or localStorage
                const activeTab = window.location.hash || localStorage.getItem('sellerActiveTab') || '#products';

                // Activate the tab
                const tab = new bootstrap.Tab(document.querySelector(`[data-bs-target="${activeTab}"]`));
                tab.show();

                // Store active tab when changed
                const tabElements = document.querySelectorAll('button[data-bs-toggle="tab"]');
                tabElements.forEach(tab => {
                    tab.addEventListener('shown.bs.tab', function (event) {
                        const targetId = event.target.dataset.bsTarget;
                        localStorage.setItem('sellerActiveTab', targetId);
                        window.location.hash = targetId;
                    });
                });
            });
            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        }

        function deleteProduct(productId) {
            document.getElementById('delete_product_id').value = productId;
            new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
        }

        // Function to update order status
        function updateOrderStatus(orderId) {
            document.getElementById('status_order_id').value = orderId;
            new bootstrap.Modal(document.getElementById('updateOrderStatusModal')).show();
        }

        // Function to delete order
        function deleteOrder(orderId) {
            document.getElementById('delete_order_id').value = orderId;
            new bootstrap.Modal(document.getElementById('deleteOrderModal')).show();
        }

        // Function to filter orders
        function filterOrders(status) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>