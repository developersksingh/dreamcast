<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management</title>
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}"  rel="stylesheet">
    <link href="{{ asset('frontend/css/dataTables.min.css') }}" rel="stylesheet">

</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">User Registration</h2>

                        <div class="alert alert-success alert-dismissible fade" role="alert" id="success-alert">
                            <strong>Record Saved Successfully!</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <div class="alert alert-danger alert-dismissible fade" role="alert" id="error-alert">
                            <strong>Some technical error!</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="userForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}">
                                    <div class="text-danger"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}">
                                    <div class="text-danger"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
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
                                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
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
                    <!-- Table rows will be populated here by AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="{{ asset('frontend/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{ asset('frontend/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('frontend/js/dataTables.min.js')}}"></script>


    <script>
        $(document).ready(function() {

            const validationRules = {
                name: { required: true, message: 'Please enter your name' },
                email: { required: true, message: 'Please enter a valid email address' },
                phone: { required: true, message: 'Please enter a valid indian phone number' },
                role_id: { required: true, message: 'Please select a role' },
                profile_image: { required: true, maxSize: 2048, allowedTypes: ['image/jpeg', 'image/png'], message: 'Please select a valid image (max 2 MB)' },
                description: { required: true, message: 'Please enter a description' }
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
                            $('#success-alert').addClass('show');
                            setTimeout(function() {
                            $('#success-alert').fadeOut('slow');
                                 $('#success-alert').removeClass('show');
                            }, 3000);
                            $('#userForm')[0].reset();
                            addUserToTable(); // Refresh the table
                        },
                        error: function(xhr) {
                            $('.text-danger').text(''); // Clear errors

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                $.each(errors, function(field, messages) {
                                    $(`[name=${field}]`).next('.text-danger').text(messages[0]);
                                });
                            } else {

                                $('#error-alert').addClass('show');
                                setTimeout(function() {
                                $('#error-alert').fadeOut('slow');
                                    $('#error-alert').removeClass('show');
                                }, 5000);
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
                            const imageUrl = user.profile_image ? `{{ asset('storage') }}/${user.profile_image}` : '';
                            const imageTag = imageUrl ? `<img src="${imageUrl}" alt="Profile Image" width="50" class="img-thumbnail">` : `<img src="" alt="Profile Image" width="50" class="img-thumbnail" style="display:none;">`;
                            new DataTable('#usersTable');
                            const row = [
                                imageTag,
                                user.name,
                                user.email,
                                user.phone,
                                user.description,
                                user.role.name
                            ];
                            $('#usersTable').DataTable().row.add(row).draw();
                        });
                    },
                    error: function() {
                        $('#error-alert').addClass('show');
                                setTimeout(function() {
                                $('#error-alert').fadeOut('slow');
                                    $('#error-alert').removeClass('show');
                                }, 5000);
                    }
                });
            }
            addUserToTable();
        });
    </script>
</body>
</html>
