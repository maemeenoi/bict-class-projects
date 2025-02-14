</html>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>##product_name## - Agora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="##site##">Agora</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="##site##">
                    <i class="bi bi-house-door"></i> Home
                </a>
                <a class="nav-link" href="##site##/buyer/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a class="nav-link" href="##site##/logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="##site##">Home</a></li>
                        <li class="breadcrumb-item"><a href="##site##/buyer/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">##product_name##</li>
                    </ol>
                </nav>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h1 class="mb-4">##product_name##</h1>

                        <div class="mb-4">
                            <span class="badge bg-primary">##category##</span>
                            <span class="ms-2 text-muted">Sold by: ##seller_name## (##business_name##)</span>
                        </div>

                        <div class="mb-4">
                            <h2 class="h4">Description</h2>
                            <p>##description##</p>
                        </div>

                        <div class="mb-4">
                            <h2 class="h4">Price</h2>
                            <p class="h2 text-primary">$##price##</p>
                        </div>

                        <div class="mb-4">
                            <h2 class="h4">Availability</h2>
                            <p>##stock_quantity## units in stock</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 mb-4">Purchase Options</h2>

                        <form method="post" action="##site##/buyer/dashboard" class="needs-validation" novalidate>
                            <input type="hidden" name="action" value="buy_now">
                            <input type="hidden" name="product_id" value="##product_id##">

                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" required min="1"
                                    max="##stock_quantity##" value="1">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Shipping Address</label>
                                <textarea name="shipping_address" class="form-control" required
                                    rows="3">##buyer_address##</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Order Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="2"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Buy Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>