<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management</title>
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/toast.style.min.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card">

                    <div class="card-body">
                        <form id="userForm" enctype="multipart/form-data">
                            <div class="row">

                                <div class="card-header">
                                    <h2 class="text-center">User Registration</h2>
                                </div>

                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name') }}">
                                    <div class="text-danger"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ old('email') }}">
                                    <div class="text-danger"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        value="{{ old('phone') }}">
                                    <div class="text-danger"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="role_id" class="form-label">Role</label>
                                    <select class="form-select" id="role_id" name="role_id">
                                        <option value="">Select a role</option>
                                        @foreach ($roles as $userrole)
                                            <option value="{{ $userrole->id }}">{{ $userrole->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="profile_image" class="form-label">Profile Image</label>
                                    <input type="file" class="form-control" id="profile_image" name="profile_image"
                                        accept="image/*">
                                    <div class="text-danger"></div>
                                </div>
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
                                    <div class="text-danger"></div>
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
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Description</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script src="{{ asset('frontend/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('frontend/js/toast.script.js') }}"></script>
    <script>
        $(document).ready(function() {
            const toastoptions = {

                stack: true,
                position_class: "toast-top-right",
                fullscreen: false,
                width: 250,
                spacing: 20,
                timeout: 4000,
                has_close_btn: true,
                has_icon: false,
                sticky: false,
                border_radius: 6,
                has_progress: true,
                rtl: false
            };

            const validationRules = {
                name: {
                    required: true,
                    message: 'Please enter your name'
                },
                email: {
                    required: true,
                    message: 'Please enter a valid email address'
                },
                phone: {
                    required: true,
                    message: 'Please enter a valid Indian phone number'
                },
                role_id: {
                    required: true,
                    message: 'Please select a role'
                },
                profile_image: {
                    required: true,
                    maxSize: 2048,
                    allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
                    message: 'Please select a valid image (max size 2 MB)'
                },
                description: {
                    required: true,
                    message: 'Please enter a description'
                }
            };

            function validateImage(file) {
                if (!file) return true;
                if (file.size > validationRules.profile_image.maxSize * 1024) return false;
                if (!validationRules.profile_image.allowedTypes.includes(file.type)) return false;
                return true;
            }

            function validateForm() {
                let isValid = true;
                $.each(validationRules, function(field, rule) {
                    const $input = $(`[name=${field}]`);
                    const $errorElement = $input.next('.text-danger');

                    if (rule.required && !$input.val().trim()) {
                        isValid = false;
                        $errorElement.text(rule.message);
                    } else if (field === 'profile_image' && !validateImage($input[0].files[0])) {
                        isValid = false;
                        $errorElement.text(rule.message);
                    } else {
                        $errorElement.text('');
                    }
                });
                return isValid;
            }

            $('#userForm').on('submit', function(e) {
                e.preventDefault();

                if (validateForm()) {
                    const formData = new FormData(this);

                    $.ajax({
                        url: '/create-user',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            $.Toast("Success", "user created successfully", "success",
                                toastoptions);

                            $('#userForm')[0].reset();
                            addUserToTable();
                        },
                        error: function(xhr) {
                            $('.text-danger').text('');

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                $.each(errors, function(field, messages) {
                                    $(`[name=${field}]`).next('.text-danger').text(
                                        messages[0]);
                                });
                            } else {
                                $.Toast("Error", "An error occurred", "error", toastoptions);
                            }
                        }
                    });
                }
            });

            $('#reset_btn').on('click', function() {
                $('#userForm')[0].reset();
                $('.text-danger').text('');
            });

            function addUserToTable() {
                $.ajax({
                    url: '{{ route('users.data') }}',
                    method: 'GET',
                    success: function(response) {
                        const usersTableBody = $('#usersTable tbody');
                        usersTableBody.empty();

                        $.each(response, function(index, user) {
                            const imageUrl = user.profile_image ?
                                `{{ asset('storage') }}/${user.profile_image}` : '';
                            const imageTag = imageUrl ?
                                `<img src="${imageUrl}" alt="Profile Image" width="50" class="img-thumbnail">` :
                                `<img src="" alt="Profile Image" width="50" class="img-thumbnail" style="display:none;">`;

                            const row = `<tr>
                                <td>${imageTag}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.phone}</td>
                                <td>${user.description}</td>
                                <td>${user.role.name}</td>
                            </tr>`;

                            usersTableBody.append(row);
                        });

                        $('#usersTable').DataTable();
                    },
                    error: function() {
                        $.Toast("Error", "Unable to load user data", {
                            position_class: "toast-top-right",
                            icon: "error"
                        });
                    }
                });
            }

            addUserToTable();
        });
    </script>
</body>

</html>
