<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apoteka IS - Informacioni sistem udruzenih apoteka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-hospital"></i> Apoteka IS
            </a>
            <div class="navbar-nav ms-auto">
                @auth
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right"></i> Prijava
                    </a>
                    <a class="nav-link" href="{{ route('register') }}">
                        <i class="bi bi-person-plus"></i> Registracija
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Informacioni sistem udruzenih apoteka</h1>
            <p class="lead mb-5">Upravljanje zalihama, prodajom, receptima i nabavkom — sve na jednom mestu.</p>
            @auth
                <a href="{{ route('pretraga') }}" class="btn btn-light btn-lg me-3">
                    <i class="bi bi-search"></i> Pretrazi lekove
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-light btn-lg me-3">
                    <i class="bi bi-box-arrow-in-right"></i> Prijavite se
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-person-plus"></i> Registrujte se
                </a>
            @endauth
        </div>
    </section>

    <main class="container py-5">
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="feature-icon mb-3"><i class="bi bi-capsule"></i></div>
                <h4>Pretraga lekova</h4>
                <p class="text-muted">Pretrazite dostupnost lekova, uporedite cene i pronadjite najblizu apoteku.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon mb-3"><i class="bi bi-box-seam"></i></div>
                <h4>Upravljanje zalihama</h4>
                <p class="text-muted">Pratite zalihe u realnom vremenu sa upozorenjima za niske kolicine.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon mb-3"><i class="bi bi-file-medical"></i></div>
                <h4>Recepti i prodaja</h4>
                <p class="text-muted">Evidentirajte recepte, prodaje i generisajte izvestaje.</p>
            </div>
        </div>
    </main>

    <footer class="bg-light py-4 mt-auto">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} Informacioni sistem udruzenih apoteka</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
