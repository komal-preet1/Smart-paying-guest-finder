<!-- Login Modal -->
<div class="modal fade" id="login-modal" tabindex="-1" aria-labelledby="login-heading" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="login-heading">Login with PGLife</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="login-form" role="form" action="api/login_submit.php" method="POST">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" minlength="6" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>

            <div class="modal-footer">
                <span>
                    Don’t have an account?
                    <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signup-modal">
                        Signup
                    </a>
                </span>
            </div>

        </div>
    </div>
</div>
