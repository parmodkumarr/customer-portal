<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div id="loader" style="
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(255,255,255,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        display: none;
    ">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Customer Portal</a>
            <button class="btn btn-outline-light" onclick="logout()">Logout</button>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Customers</h2>
            <button class="btn btn-primary" onclick="showCreateModal()">
                <i class="bi bi-plus"></i> Add Customer
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Email</th>
                                <th>Creation Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="customersTable">
                            <!-- Customers will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm">
                        <input type="hidden" id="customerId">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="first_name" required>
                            <div class="invalid-feedback" id="first_name-error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="last_name" required>
                            <div class="invalid-feedback" id="last_name-error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" class="form-control" id="age" name="age" required>
                            <div class="invalid-feedback" id="age-error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                            <div class="invalid-feedback" id="dob-error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        <div class="alert alert-danger d-none" id="form-error"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomer()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const modal = new bootstrap.Modal(document.getElementById('customerModal'));
        const token = localStorage.getItem('access_token');

        if (!token) {
            window.location.href = '/login';
        }

        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        // Load customers
        async function loadCustomers() {
            try {
                const response = await axios.get('/api/customers');
                const customers = response.data.data;
                const tbody = document.getElementById('customersTable');
                tbody.innerHTML = '';

                showLoader();

                customers.forEach(customer => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${customer.id}</td>
                            <td>${customer.first_name} ${customer.last_name}</td>
                            <td>${customer.age}</td>
                            <td>${customer.email}</td>
                            <td>${new Date(customer.creation_date).toLocaleDateString()}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editCustomer(${JSON.stringify(customer).replace(/"/g, '&quot;')})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${customer.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } catch (error) {
                if (error.response?.status === 401) {
                    window.location.href = '/login';
                }
                alert('Error loading customers');
            } finally {
                hideLoader();
            }
        }

        // Show create modal
        function showCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add Customer';
            document.getElementById('customerForm').reset();
            document.getElementById('customerId').value = '';
            clearAllErrors();
            modal.show();
        }

        // Edit customer
        function editCustomer(customer) {
            document.getElementById('modalTitle').textContent = 'Edit Customer';
            document.getElementById('customerId').value = customer.id;
            document.getElementById('firstName').value = customer.first_name;
            document.getElementById('lastName').value = customer.last_name;
            document.getElementById('age').value = customer.age;
            document.getElementById('dob').value = customer.dob ? customer.dob.slice(0, 10) : NULL;
            document.getElementById('email').value = customer.email;
            clearAllErrors();
            modal.show();
        }

        // Save customer
        async function saveCustomer() {
            if (!ensureAuth()) return;

            const customerId = document.getElementById('customerId').value;
            const data = {
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                age: document.getElementById('age').value,
                dob: document.getElementById('dob').value,
                email: document.getElementById('email').value
            };

            // Clear previous errors
            clearAllErrors();
            showLoader();
            try {
                if (customerId) {
                    await axios.put(`/api/customers/${customerId}`, data);
                } else {
                    await axios.post('/api/customers', data);
                }
                modal.hide();
                loadCustomers();
            } catch (error) {
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    Object.keys(errors).forEach(field => {
                        showFieldError(field, errors[field][0]);
                    });
                } else {
                    showError('form-error', error.response?.data?.message || 'Error saving customer');
                }
            } finally {
                hideLoader();
            }
        }

        function showError(elementId, message) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.classList.remove('d-none');
        }

        function showFieldError(fieldId, message) {
            const input = document.getElementById(fieldId === 'first_name' ? 'firstName' :
                                                fieldId === 'last_name' ? 'lastName' : fieldId);
            const errorDiv = document.getElementById(`${fieldId}-error`);
            if (input && errorDiv) {
                input.classList.add('is-invalid');
                errorDiv.textContent = message;
            }
        }

        function clearFieldError(fieldId) {
            const input = document.getElementById(fieldId === 'first_name' ? 'firstName' :
                                                fieldId === 'last_name' ? 'lastName' : fieldId);
            const errorDiv = document.getElementById(`${fieldId}-error`);
            if (input && errorDiv) {
                input.classList.remove('is-invalid');
                errorDiv.textContent = '';
            }
        }

        function clearAllErrors() {
            const fields = ['first_name', 'last_name', 'age', 'dob', 'email'];
            fields.forEach(field => clearFieldError(field));
            const formError = document.getElementById('form-error');
            if (formError) {
                formError.textContent = '';
                formError.classList.add('d-none');
            }
        }

        // Delete customer
        async function deleteCustomer(id) {
            if (!confirm('Are you sure you want to delete this customer?')) {
                return;
            }

            try {
                showLoader();
                await axios.delete(`/api/customers/${id}`);
                loadCustomers();
            } catch (error) {
                alert('Error deleting customer');
            } finally {
                hideLoader();
            }
        }

        // Logout
        async function logout() {
            try {
                showLoader();
                await axios.post('/api/logout');
                localStorage.removeItem('access_token');
                window.location.href = '/login';
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                hideLoader();
            }
        }

        function ensureAuth() {
            const token = localStorage.getItem('access_token');
            if (!token) {
                alert('Your session has expired. Please login again.');
                window.location.href = '/login';
                return false;
            }
            return true;
        }

        function showLoader() {
            document.getElementById('loader').style.display = 'flex';
        }

        function hideLoader() {
            document.getElementById('loader').style.display = 'none';
        }

        // Initial load
        loadCustomers();
    </script>
</body>
</html>
