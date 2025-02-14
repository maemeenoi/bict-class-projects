<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - Agora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="##site##">Agora</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h2>Customer Registration</h2>
                            <p class="text-muted">Step ##step## of 2</p>
                        </div>

                        ##if:error##
                        <div class="alert alert-danger">##error##</div>
                        ##endif##

                        <form method="post" action="##site##/register" class="needs-validation" novalidate>
                            <input type="hidden" name="registration_type" value="customer">
                            <input type="hidden" name="step" value="##step##">

                            ##if:step=1##
                            <!-- Step 1: Role and Region Selection -->
                            <div class="mb-4">
                                <label class="form-label">Select Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="">Choose role...</option>
                                    <option value="Seller">Seller</option>
                                    <option value="Buyer">Buyer</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Select Region</label>
                                ##regions##
                            </div>
                            ##endif##

                            ##if:step=2##
                            <!-- Step 2: User Details and Business Selection -->
                            <input type="hidden" name="role" value="##role##">
                            <div class="mb-3">
                                <label class="form-label">Select Business</label>
                                ##businesses##
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="user_name" class="form-control" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirm" class="form-control" required
                                    minlength="8">
                            </div>
                            ##endif##

                            <div class="d-flex justify-content-between mt-4">
                                ##if:step>1##
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="history.back()">Back</button>
                                ##else##
                                <a href="##site##/register" class="btn btn-outline-secondary">Cancel</a>
                                ##endif##

                                <button type="submit" class="btn btn-primary">
                                    ##if:step=2##Complete Registration##else##Next##endif##
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>

</html>