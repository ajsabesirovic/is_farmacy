@extends('layouts.guest')

@section('title', 'Prijava')

@section('content')
<div class="text-center mb-4">
    <i class="bi bi-hospital text-primary" style="font-size: 4rem;"></i>
    <h1 class="h3 mb-3 fw-normal">Informacioni sistem apoteka</h1>
</div>

<div class="card shadow">
    <div class="card-body p-4">
        <h2 class="card-title text-center mb-4">Prijava</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email adresa</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Lozinka</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Zapamti me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right"></i> Prijavi se
            </button>
        </form>
    </div>
</div>

@endsection
