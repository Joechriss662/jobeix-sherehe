<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jolinx Event Management System</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    </head>
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">Jolinx</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @if (Route::has('login'))
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Log in</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                                    </li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <header class="bg-primary text-white text-center py-5">
            <div class="container">
                <h1>Welcome to the Event Management System</h1>
                <p class="lead">Manage your events, guests, pledges, and contributions seamlessly.</p>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">Get Started</a>
            </div>
        </header>

        <main class="container my-5">
            <div class="row">
                <div class="col-md-4">
                    <h3>Manage Events</h3>
                    <p>Create and manage events with ease.</p>
                </div>
                <div class="col-md-4 text-center">
                    <h3>Track Guests</h3>
                    <p>Keep track of your guest list and RSVPs.</p>
                </div>
                <div class="col-md-4">
                    <h3>Monitor Contributions</h3>
                    <p>Track pledges and contributions effortlessly.</p>
                </div>
            </div>
        </main>

        <footer class="bg-primary text-white py-4">
            <div class="container">
                <div class="row">
                    <!-- About Section -->
                    <div class="col-md-4">
                        <h5>About Jolinx</h5>
                        <p>
                            The Event Management System helps you seamlessly manage events, guests, pledges, and contributions. Simplify your event planning today!
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-md-4 text-center">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="https://jobeix.great-site.net" class="text-white text-decoration-none">Joberthix Company</a></li>
                           
                        </ul>
                    </div>

                    <!-- Contact Section -->
                    <div class="col-md-4">
                        <h5>Contact Us</h5>
                        <p>
                            Have questions? Reach out to us:
                        </p>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-envelope"></i> <a href="mailto:jolinx662@gmail.com" class="text-white text-decoration-none">jolinx662@gmail.com</a></li>
                            <li><i class="bi bi-envelope"></i> <a href="mailto:joberthix@gmail.com" class="text-white text-decoration-none">joberthix@gmail.com</a></li>
                            <li><i class="bi bi-telephone"></i> +255 756 578 051</li>
                            <li><i class="bi bi-geo-alt"></i> Kinondoni, Dar es salaam, Tanzania</li>
                        </ul>
                    </div>
                </div>

                <hr class="border-light">

                <!-- Bottom Footer -->
                <div class="text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} Jolinx Event Management System | Powered by Joberthix Company.</p>
                </div>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
