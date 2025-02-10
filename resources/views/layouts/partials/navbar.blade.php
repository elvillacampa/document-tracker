<style type="">
@media (max-width: 768px) {
    .btn {
        width: 100%; /* Buttons take full width on mobile */
        margin-bottom: 5px;
    }

    .text-center-name{
        text-align: center;
    }


}
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">Strategy Management Branch</a>
        <div class="ms-auto text-end row" style="padding:0px;">
            @auth
                <!-- Display the user's name -->

                <!-- Horizontal Button Group for user actions -->
                <div class="btn-group mt-2 col-md-12 col-12 row " style="padding-right: 0px;" role="group" aria-label="User actions">
                    <div class="col-md-12 col-12 text-center-name" style="padding-top: 6px;">
                        <span class="navbar-text" >Hello, {{ Auth::user()->name}}</span>
                    </div>
                    <a style="padding:.2rem .5rem;font-size:.875rem;border-radius:.2rem" class="m-1 btn btn-secondary col-12 col-md-3" href="{{ route('password.change') }}">Change Password</a>
                    <a style="padding:.2rem .5rem;font-size:.875rem;border-radius:.2rem" class="m-1 btn btn-secondary col-12 col-md-2" href="{{ route('profile.edit') }}">Edit Profile</a>
                    @if(Auth::user()->role === 'admin')
                        <a style="padding:.2rem .5rem;font-size:.875rem;border-radius:.2rem"class="m-1 btn btn-warning col-12 col-md-3" href="{{ route('admin.users.index') }}">Admin Menu</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="col-12 col-md-2" style="margin-right: -10px;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger ">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-link">Login</a>
            @endauth
        </div>
    </div>
</nav>


