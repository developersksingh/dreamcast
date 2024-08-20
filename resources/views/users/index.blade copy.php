<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">User Registration</h2>
                    </div>
                    <div class="card-body">
                        <form id="userForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                                <div class="col-md-6">
                                    <label for="role_id" class="form-label">Role</label>
                                    <select class="form-select" id="role_id" name="role_id">
                                        <option value="">Select a role</option>
                                        <option value="1">Admin</option>
                                        <option value="2">User</option>
                                        <option value="3">Guest</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                </div>
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" class="btn btn-primary" id="reset_btn">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-5">Users</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="usersTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Description</th>
                        <th>Role</th>
                        <th>Profile Image</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->description }}</td>
                            <td>{{ $user->role->name }}</td>
                            <td>
                                @if ($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" width="50" class="img-thumbnail">
                                @else
                                    No Image
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('userForm');
            const resetButton = document.getElementById('reset_btn');

            const validationRules = {
                name: {
                    required: true,
                    message: 'Please enter your name'
                },
                email: {
                    required: true,
                    email: true,
                    message: 'Please enter a valid email address'
                },
                phone: {
                    required: true,
                    indianPhone: true,
                    message: 'Please enter a valid Indian phone number'
                },
                role_id: {
                    required: true,
                    message: 'Please select a role'
                },
                description: {
                    required: true,
                    message: 'Please enter a description'
                },
                profile_image: {
                    required: false,
                    image: true,
                    maxSize: 2048,
                    message: 'Please select a valid image file (max size 2 MB)'
                }
            };

            function validateIndianPhoneNumber(phone) {
                const cleanPhone = phone.replace(/\D/g, '');
                return cleanPhone.length === 10 && /^[6-9]\d{9}$/.test(cleanPhone);
            }

            function validateImage(file) {
                if (!file) {
                    return true; // No file selected, skip validation
                }

                if (!file.type.startsWith('image/')) {
                    return false; // Not an image file
                }

                if (file.size > validationRules.profile_image.maxSize * 1024) {
                    return false; // File size exceeds the limit
                }

                return true;
            }

            function validateForm() {
                let isValid = true;
                Object.keys(validationRules).forEach(field => {
                    const input = form.elements[field];
                    const rule = validationRules[field];
                    let errorElement = input.nextElementSibling;

                    if (!errorElement || !errorElement.classList.contains('text-danger')) {
                        errorElement = document.createElement('div');
                        errorElement.classList.add('text-danger');
                        input.parentNode.insertBefore(errorElement, input.nextSibling);
                    }

                    if (rule.required && !input.value.trim()) {
                        isValid = false;
                        errorElement.textContent = rule.message;
                    } else if (rule.email && !/\S+@\S+\.\S+/.test(input.value)) {
                        isValid = false;
                        errorElement.textContent = rule.message;
                    } else if (rule.indianPhone && !validateIndianPhoneNumber(input.value)) {
                        isValid = false;
                        errorElement.textContent = rule.message;
                    } else if (field === 'profile_image' && !validateImage(input.files[0])) {
                        isValid = false;
                        errorElement.textContent = rule.message;
                    } else {
                        errorElement.textContent = '';
                    }
                });
                return isValid;
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateForm()) {
                    const formData = new FormData(form);

                    fetch('/create-user', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Assuming 'data' contains the newly created user information
                            addUserToTable(data.user);
                            alert('Form submitted successfully!');
                            form.reset();
                        })
                        .catch(error => {
                            alert('An error occurred. Please try again.');
                            console.log(error);
                        });
                }
            });

            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                form.reset();
                document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
            });

            function addUserToTable(user) {
                const tableBody = document.querySelector('#usersTable tbody');
                const newRow = document.createElement('tr');

                newRow.innerHTML = `
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.phone}</td>
                    <td>${user.description}</td>
                    <td>${user.role.name}</td>
                    <td>
                        ${user.profile_image ?
                            `<img src="${user.profile_image_url}" alt="Profile Image" width="50" class="img-thumbnail">` :
                            'No Image'
                        }
                    </td>
                `;

                tableBody.appendChild(newRow);
            }
        });
    </script>
</body>
</html>
