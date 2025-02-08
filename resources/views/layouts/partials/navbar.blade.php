<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">Strategy Management Branch</a>
        <div class="ms-auto text-end">
            @auth
                <!-- Display the user's name -->
                <div>
                    <span class="navbar-text">Hello, {{ Auth::user()->name }}</span>
                </div>
                <!-- Horizontal Button Group for user actions -->
                <div class="btn-group mt-2" role="group" aria-label="User actions">
                    <a style="padding:.2rem .5rem;font-size:.875rem;border-radius:.2rem" class="m-1 btn btn-sm btn-secondary" href="{{ route('password.change') }}">Change Password</a>
                    <a style="padding:.2rem .5rem;font-size:.875rem;border-radius:.2rem" class="m-1 btn btn-sm btn-secondary" href="{{ route('profile.edit') }}">Edit Profile</a>
                    @if(Auth::user()->role === 'admin')
                        <a style="padding:.2rem .5rem;font-size:.875rem;border-radius:.2rem"class="m-1 btn btn-sm btn-secondary" href="{{ route('admin.users.index') }}">Admin Menu</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class=" m-1 btn btn-sm btn-secondary">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-link">Login</a>
            @endauth
        </div>
    </div>
</nav>


