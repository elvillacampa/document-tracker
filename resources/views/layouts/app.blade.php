<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Document Flow Tracker</title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap.min.css') }}">
    <script src="{{ asset('assets/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/jquery-3.6.0.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap CSS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script> -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap2.min.css') }}">
    <script src="{{ asset('assets/html2canvas.min.js') }}"></script>
    <script src="{{ asset('assets/jspdf.umd.min.js') }}"></script>
    <script src="{{ asset('assets/jquery2-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/xlsx.full.min.js') }}"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">Document Flow Tracker</a>
        <!-- Other navigation links can go here -->

        <div class="d-flex align-items-center">
            @auth
                <span class="navbar-text me-3">
                    Hello, {{ Auth::user()->name }}
                </span>
                <a href="{{ route('password.change') }}" class="btn btn-link me-3">Change Password</a>
                <a href="{{ route('profile.edit') }}" class="btn btn-link me-3">Edit Profile</a>
            @endauth
            <!-- Logout form -->
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-link">Logout</button>
            </form>
        </div>
    </div>
</nav>


    <div class="container-fluid p-5 my-5">
        <h1 class="text-center">SMB</h1>
        @yield('content')
    </div>
<!-- Bootstrap JS Bundle (includes Popper.js) -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="{{ asset('assets/bootstrap2.bundle.min.js') }}"></script>
@section('content')
<style>

    button{
        margin: 2px!important;
    }
}


</style>
@section('content')
</body>
</html>
