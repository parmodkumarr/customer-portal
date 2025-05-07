<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <div id="loginForm">
                            <form id="login">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="invalid-feedback" id="password-error"></div>
                                </div>
                                <div class="alert alert-danger d-none" id="login-error"></div>
                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                        <div id="mfaForm" style="display: none;">
                            <form id="mfa">
                                <div class="mb-3">
                                    <label for="mfa_token" class="form-label">MFA Token</label>
                                    <input type="text" class="form-control" id="mfa_token" name="mfa_token" required>
                                    <div class="invalid-feedback" id="mfa_token-error"></div>
                                    <small class="text-muted">Please enter the token sent to your email.</small>
                                </div>
                                <div class="alert alert-danger d-none" id="mfa-error"></div>
                                <button type="submit" class="btn btn-primary w-100">Verify</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const loginForm = document.getElementById('login');
        const mfaForm = document.getElementById('mfa');
        const loginFormDiv = document.getElementById('loginForm');
        const mfaFormDiv = document.getElementById('mfaForm');
        let userEmail = '';

        function showError(elementId, message) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.classList.remove('d-none');
        }

        function hideError(elementId) {
            const element = document.getElementById(elementId);
            element.textContent = '';
            element.classList.add('d-none');
        }

        function showFieldError(fieldId, message) {
            const input = document.getElementById(fieldId);
            const errorDiv = document.getElementById(`${fieldId}-error`);
            input.classList.add('is-invalid');
            errorDiv.textContent = message;
        }

        function clearFieldError(fieldId) {
            const input = document.getElementById(fieldId);
            const errorDiv = document.getElementById(`${fieldId}-error`);
            input.classList.remove('is-invalid');
            errorDiv.textContent = '';
        }

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            hideError('login-error');
            clearFieldError('email');
            clearFieldError('password');

            try {
                const response = await axios.post('/api/login', {
                    email: formData.get('email'),
                    password: formData.get('password')
                });

                userEmail = formData.get('email');
                loginFormDiv.style.display = 'none';
                mfaFormDiv.style.display = 'block';
            } catch (error) {
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    if (errors.email) showFieldError('email', errors.email[0]);
                    if (errors.password) showFieldError('password', errors.password[0]);
                } else {
                    showError('login-error', error.response?.data?.message || 'Login failed');
                }
            }
        });

        mfaForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(mfaForm);
            hideError('mfa-error');
            clearFieldError('mfa_token');

            try {
                const response = await axios.post('/api/verify-mfa', {
                    email: userEmail,
                    mfa_token: formData.get('mfa_token')
                });

                localStorage.setItem('access_token', response.data.access_token);
                window.location.href = '/customers';
            } catch (error) {
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    if (errors.mfa_token) showFieldError('mfa_token', errors.mfa_token[0]);
                } else {
                    showError('mfa-error', error.response?.data?.message || 'MFA verification failed');
                }
            }
        });
    </script>
</body>
</html>
