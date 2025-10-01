<?= $this->extend('templates/auth_layout') ?>

<?= $this->section('title') ?>Login<?= $this->endSection() ?>

<?= $this->section('main') ?>
<div class="container p-5 min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="card col-12 col-md-5 shadow-sm">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">
                <i class="bi bi-fork-knife pe-none me-2" style="font-size: 24pt;"></i>
                <?= esc(env('app.name')) ?>
            </h4>
            <h5 class="card-title mb-3">Login</h5>

            <div id="alert-parent"></div>

            <form method="post" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="Email" value="<?= old('email') ?>" required>
                    <label for="floatingEmailInput">Email</label>
                    <div class="invalid-feedback">
                        Please provide a valid email.
                    </div>
                </div>

                <!-- Password -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPasswordInput" name="password" inputmode="text" autocomplete="current-password" placeholder="Password" required>
                    <label for="floatingPasswordInput">Password</label>
                    <div class="invalid-feedback">
                        Please provide a valid password.
                    </div>
                </div>

                <div class="d-grid mx-auto m-3">
                    <button id="submitButton" type="submit" class="btn btn-primary btn-block">Login</button>
                </div>

            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<?= $this->include('templates/scripts/input_validator_script') ?>
<?= $this->include('templates/scripts/alert_script') ?>

<script>
    const LOGIN_ENDPOINT = '<?= url_to('api/login') ?>';

    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('floatingEmailInput');
        const passwordInput = document.getElementById('floatingPasswordInput');
        const submitButton = document.getElementById('submitButton');
        const form = document.querySelector('.needs-validation');
        const alertParent = document.querySelector('#alert-parent');

        const validateEmail = () => validateInputForm(
            emailInput,
            (value) => {
                if (value.length < 3 || !value.includes('@')) {
                    return 'Please provide a valid email'
                }
            },
            emailInput.nextElementSibling.nextElementSibling
        );

        const validatePassword = () => validateInputForm(
            passwordInput,
            (value) => {
                if (value.length < 4) {
                    return 'Please provide a valid password (at least 4 characters).'
                }
            },
            passwordInput.nextElementSibling.nextElementSibling
        );

        emailInput.addEventListener('input', validateEmail);
        passwordInput.addEventListener('input', validatePassword);

        let validators = [validateEmail, validatePassword];

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            event.stopPropagation();

            if (validators.every(fn => fn())) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Signing in...';
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-secondary');

                await login();

                submitButton.disabled = false;
                submitButton.innerHTML = 'Login';
                submitButton.classList.remove('btn-secondary');
                submitButton.classList.add('btn-primary');
            }
        });

        async function login() {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            await fetch(LOGIN_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                    credentials: 'include',
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    if (data.error) {
                        if (data.message) {
                            showAlert(alertParent, data.message, 'danger');
                        } else if (data.messages) {
                            showAlert(alertParent, data.messages, 'danger');
                        }
                        return;
                    }

                    // clear previous alerts
                    clearAlerts(alertParent);

                    showAlert(alertParent, data.message, 'success', false);

                    // save token to local storage
                    localStorage.setItem('jwt_token', data.jwt_token);

                    // Redirect
                    window.location.href = data.redirect_url;
                })
                .catch((error) => {
                    console.error('Error:', error);
                    showAlert(alertParent, 'An error occurred. Please try again later.', 'danger');
                });
        }
    });
</script>
<?= $this->endSection() ?>