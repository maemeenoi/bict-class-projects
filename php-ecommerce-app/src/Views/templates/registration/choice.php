<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Agora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="##site##">Agora</a>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center mb-4">Join Agora</h1>

        ##feedback##

        <div class="row justify-content-center g-4">
            <div class="col-md-6 col-lg-5">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Business Registration</h5>
                        <p class="card-text">Register your business to join Agora marketplace</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Create your business
                                profile</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Manage sellers in your
                                region</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Access business
                                dashboard</li>
                        </ul>
                        <form method="get" action="##site##/register">
                            <input type="hidden" name="registration_type" value="business">
                            <button type="submit" class="btn btn-primary w-100">Register Business</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-5">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Customer Registration</h5>
                        <p class="card-text">Join as a seller or buyer</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Choose your role
                                (Seller/Buyer)</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Select your region
                            </li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Connect with local
                                businesses</li>
                        </ul>
                        <form method="get" action="##site##/register">
                            <input type="hidden" name="registration_type" value="customer">
                            <button type="submit" class="btn btn-primary w-100">Register as Customer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <p>Already have an account? <a href="##site##/login">Login here</a></p>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>