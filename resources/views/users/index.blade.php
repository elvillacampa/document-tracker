@extends('layouts.app')

@section('content')
<div class="container">
    <h2>User Management</h2>


    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Approved</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr data-user-id="{{ $user->id }}">
                    <td>{{ $user->id }}</td>
                    <td class="user-name">{{ $user->name }}</td>
                    <td class="user-email">{{ $user->email }}</td>
                    <td>
                        @if($user->approved)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-warning">No</span>
                        @endif
                    </td>
                    <td class="user-role">{{ ucfirst($user->role) }}</td>
                    <td>
                        @if(!$user->approved)
                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>
                        @endif

                        <!-- Edit button triggers the Edit User modal -->
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $user->id }}">
                            Edit
                        </button>

                        <!-- Change Password button triggers the Change Password modal -->
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#passwordModal-{{ $user->id }}">
                            Change Password
                        </button>
                    </td>
                </tr>

                <!-- Edit User Modal -->
                <div class="modal fade" id="editModal-{{ $user->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form class="ajax-edit-user" data-user-id="{{ $user->id }}" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel-{{ $user->id }}">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Name -->
                                    <div class="mb-3">
                                        <label for="name-{{ $user->id }}" class="form-label">Name</label>
                                        <input type="text" name="name" id="name-{{ $user->id }}" class="form-control" value="{{ $user->name }}" required>
                                    </div>
                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email-{{ $user->id }}" class="form-label">Email</label>
                                        <input type="email" name="email" id="email-{{ $user->id }}" class="form-control" value="{{ $user->email }}" required>
                                    </div>
                                    <!-- Role -->
                                    <div class="mb-3">
                                        <label for="role-{{ $user->id }}" class="form-label">Role</label>
                                        <select name="role" id="role-{{ $user->id }}" class="form-select" required>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="encoder" {{ $user->role == 'encoder' ? 'selected' : '' }}>Encoder</option>
                                            <option value="viewer" {{ $user->role == 'viewer' ? 'selected' : '' }}>Viewer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password Modal -->
                <div class="modal fade" id="passwordModal-{{ $user->id }}" tabindex="-1" aria-labelledby="passwordModalLabel-{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form class="ajax-change-password" data-user-id="{{ $user->id }}" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="passwordModalLabel-{{ $user->id }}">Change Password</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Hidden field to include role so that validation passes -->
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                    <!-- New Password -->
                                    <div class="mb-3">
                                        <label for="password-{{ $user->id }}" class="form-label">New Password</label>
                                        <input type="password" name="password" id="password-{{ $user->id }}" class="form-control" required>
                                    </div>
                                    <!-- Confirm New Password -->
                                    <div class="mb-3">
                                        <label for="password_confirmation-{{ $user->id }}" class="form-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation-{{ $user->id }}" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<!-- jQuery (ensure it's loaded before your AJAX scripts) -->
<script>
    // Helper function to show a Bootstrap toast
    $(document).ready(function() {
        // AJAX submission for Edit User form - no page reload
        $('.ajax-edit-user').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var userId = form.data('user-id');
            $.ajax({
                url: form.attr('action'),
                method: 'POST', // method spoofing via _method=PUT
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    // Update the corresponding table row with the updated values
                    var row = $('tr[data-user-id="' + userId + '"]');
                    row.find('.user-name').text(response.user.name);
                    row.find('.user-email').text(response.user.email);
                    row.find('.user-role').text(response.user.role.charAt(0).toUpperCase() + response.user.role.slice(1));
                    // Close the modal
                    $('#editModal-' + userId).modal('hide');
                    // Show a success toast
                    alert("User updated successfully!");
                },
                error: function(xhr) {
                    alert("An error occurred while updating the user.");
                }
            });
        });

        // AJAX submission for Change Password form - no page reload
        $('.ajax-change-password').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var userId = form.data('user-id');
            $.ajax({
                url: form.attr('action'),
                method: 'POST', // method spoofing using _method=PUT
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    // Close the modal
                    $('#passwordModal-' + userId).modal('hide');
                    // Show a success toast
                    alert("Password updated successfully!");
                },
                error: function(xhr) {
                    alert("An error occurred while updating the password.");
                }
            });
        });
    });
</script>
@endsection
