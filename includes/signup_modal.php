<!-- Signup Modal -->
<div class="modal fade" id="signup-modal" tabindex="-1" aria-labelledby="signup-heading" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="signup-heading">Signup with PGLife</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="signup-form" action="api/signup_submit.php" method="POST">

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                        <input type="text" class="form-control" name="phone" placeholder="Phone (10 digit)"
                               maxlength="10" minlength="10" required pattern="[6-9]{1}[0-9]{9}">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" minlength="6" required>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-university"></i></span>
                        <input type="text" class="form-control" name="college_name" placeholder="College Name" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">I am a:</label><br>
                        <input type="radio" name="gender" value="male" required class="ms-2"> Male
                        <input type="radio" name="gender" value="female" required class="ms-3"> Female
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
            </div>

            <div class="modal-footer">
                Already have an account?
                <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#login-modal">
                    Login
                </a>
            </div>

        </div>
    </div>
</div>
