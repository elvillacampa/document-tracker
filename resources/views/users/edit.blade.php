@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2>Edit User</h2>
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <!-- Role Selection -->
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" id="role" class="form-control">
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="encoder" {{ $user->role === 'encoder' ? 'selected' : '' }}>Encoder</option>
                    <option value="viewer" {{ $user->role === 'viewer' ? 'selected' : '' }}>Viewer</option>
                </select>
            </div>

            <!-- Password Change -->
            <div class="form-group">
                <label for="password">New Password (optional):</label>
                <input type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">
                    Leave blank if you do not wish to change the password.
                </small>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
</div>
@endsection
