s<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Registration - Agora</title>
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
                            <h2>Business Registration</h2>
                            <p class="text-muted">Step ##step## of 3</p>
                        </div>

                        ##if:error##
                        <div class="alert alert-danger">##error##</div>
                        ##endif##

                        <form method="post" action="##site##/register" class="needs-validation" novalidate>
                            <input type="hidden" name="registration_type" value="business">
                            <input type="hidden" name="step" value="##step##">

                            ##if:step=1##
                            <!-- Step 1: Region Selection -->
                            <div class="mb-4">
                                <label class="form-label">Select Region</label>
                                ##regions##
                                <div class="form-text">Choose the region where your business operates</div>
                            </div>
                            ##endif##

                            ##if:step=2##
                            <!-- Step 2: Business Details -->
                            <input type="hidden" name="region_id" value="##region_id##">
                            <div class="mb-3">
                                <label class="form-label">Business Name</label>
                                <input type="text" name="business_name" class="form-control" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location Name</label>
                                <input type="text" name="location_name" class="form-control" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" required maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Business Logo URL</label>
                                <input type="text" name="business_logo" class="form-control" maxlength="255">
                                <div class="form-text">Enter the URL for your business logo image</div>
                            </div>
                            ##endif##

                            ##if:step=3##
                            <!-- Step 3: Admin Account -->
                            <input type="hidden" name="business_id" value="##business_id##">
                            <div class="mb-3">
                                <label class="form-label">Admin Name</label>
                                <input type="text" name="admin_name" class="form-control" required maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Admin Email</label>
                                <input type="email" name="admin_email" class="form-control" required maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <div class="input-group">
                                    <input type="text" name="admin_address" id="admin_address" class="form-control"
                                        maxlength="255">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="useBusinessAddress()">Use Business Address</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <div class="input-group">
                                    <input type="tel" name="admin_phone" id="admin_phone" class="form-control"
                                        maxlength="20">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="useBusinessPhone()">Use Business Phone</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="admin_password" class="form-control" required
                                    minlength="8">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="admin_password_confirm" class="form-control" required
                                    minlength="8">
                            </div>

                            <!-- Add JavaScript for auto-fill functionality -->
                            <script>
                                let businessData = {
                                    address: "##business_address##",
                                    phone: "##business_phone##"
                                };

                                function useBusinessAddress() {
                                    document.getElementById('admin_address').value = businessData.address;
                                }

                                function useBusinessPhone() {
                                    document.getElementById('admin_phone').value = businessData.phone;
                                }
                            </script>
                            ##endif##

                            <div class="d-flex justify-content-between mt-4">
                                ##if:step>1##
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="history.back()">Back</button>
                                ##else##
                                <a href="##site##/register" class="btn btn-outline-secondary">Cancel</a>
                                ##endif##

                                <button type="submit" class="btn btn-primary">
                                    ##if:step=3##Complete Registration##else##Next##endif##
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