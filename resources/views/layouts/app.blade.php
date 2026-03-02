<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Apoteka IS') - Informacioni sistem apoteka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .content-wrapper {
            min-height: calc(100vh - 56px);
        }
        .card-stat {
            border-left: 4px solid;
        }
        .card-stat.primary { border-left-color: #0d6efd; }
        .card-stat.success { border-left-color: #198754; }
        .card-stat.warning { border-left-color: #ffc107; }
        .card-stat.danger { border-left-color: #dc3545; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-hospital"></i> Apoteka IS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(Auth::user()->isRegistrovaniKorisnik())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('pretraga') }}">
                            <i class="bi bi-search"></i> Pretraga lekova
                        </a>
                    </li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->puno_ime }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted">{{ Auth::user()->tip->label() }}</span></li>
                            @if(Auth::user()->apoteka)
                                <li><span class="dropdown-item-text text-muted">{{ Auth::user()->apoteka->naziv }}</span></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Odjavi se
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        @if(!Auth::user()->isRegistrovaniKorisnik())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isRegistrovaniKorisnik())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pretraga') ? 'active' : '' }}" href="{{ route('pretraga') }}">
                                <i class="bi bi-search"></i> Pretraga lekova
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isFarmaceut() || Auth::user()->isAdminApoteke())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('prodaje.*') ? 'active' : '' }}" href="{{ route('prodaje.index') }}">
                                <i class="bi bi-cart"></i> Prodaja
                            </a>
                        </li>
                        @endif

                        @if(!Auth::user()->isRegistrovaniKorisnik())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('zalihe.*') ? 'active' : '' }}" href="{{ route('zalihe.index') }}">
                                <i class="bi bi-box-seam"></i> Zalihe
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('recepti.*') ? 'active' : '' }}" href="{{ route('recepti.index') }}">
                                <i class="bi bi-file-medical"></i> Recepti
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lekovi.*') ? 'active' : '' }}" href="{{ route('lekovi.index') }}">
                                <i class="bi bi-capsule"></i> Lekovi
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isAdminApoteke() || Auth::user()->isCentralniAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('narudzbenice.*') ? 'active' : '' }}" href="{{ route('narudzbenice.index') }}">
                                <i class="bi bi-clipboard-check"></i> Narudzbenice
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dobavljaci.*') ? 'active' : '' }}" href="{{ route('dobavljaci.index') }}">
                                <i class="bi bi-truck"></i> Dobavljaci
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isCentralniAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('apoteke.*') ? 'active' : '' }}" href="{{ route('apoteke.index') }}">
                                <i class="bi bi-building"></i> Apoteke
                            </a>
                        </li>
                        @endif

                        @if(Auth::user()->isAdminApoteke() || Auth::user()->isCentralniAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('korisnici.*') ? 'active' : '' }}" href="{{ route('korisnici.index') }}">
                                <i class="bi bi-people"></i> Korisnici
                            </a>
                        </li>

                        <li class="nav-item mt-3">
                            <span class="nav-link text-muted"><strong>Izvestaji</strong></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.prodaja') ? 'active' : '' }}" href="{{ route('reports.prodaja') }}">
                                <i class="bi bi-graph-up"></i> Prodaja
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.zalihe') ? 'active' : '' }}" href="{{ route('reports.zalihe') }}">
                                <i class="bi bi-bar-chart"></i> Zalihe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.lekovi') ? 'active' : '' }}" href="{{ route('reports.lekovi') }}">
                                <i class="bi bi-list-stars"></i> Najtrazeniji lekovi
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper">
                <div class="pt-3 pb-2 mb-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
