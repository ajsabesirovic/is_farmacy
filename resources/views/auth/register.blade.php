@extends('layouts.guest')

@section('title', 'Registracija')

@section('content')
<div class="text-center mb-4">
    <i class="bi bi-hospital text-primary" style="font-size: 4rem;"></i>
    <h1 class="h3 mb-3 fw-normal">Informacioni sistem apoteka</h1>
</div>

<div class="card shadow">
    <div class="card-body p-4">
        <h2 class="card-title text-center mb-4">Registracija</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ime" class="form-label">Ime</label>
                    <input type="text" class="form-control @error('ime') is-invalid @enderror"
                           id="ime" name="ime" value="{{ old('ime') }}" required autofocus>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="prezime" class="form-label">Prezime</label>
                    <input type="text" class="form-control @error('prezime') is-invalid @enderror"
                           id="prezime" name="prezime" value="{{ old('prezime') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email adresa</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label for="jmbg" class="form-label">JMBG</label>
                <input type="text" class="form-control @error('jmbg') is-invalid @enderror"
                       id="jmbg" name="jmbg" value="{{ old('jmbg') }}" required maxlength="13"
                       pattern="[0-9]{13}" title="JMBG mora sadrzati tacno 13 cifara">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Lozinka</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Potvrdite lozinku</label>
                <input type="password" class="form-control"
                       id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-person-plus"></i> Registruj se
            </button>
        </form>

        <div class="text-center mt-3">
            <span class="text-muted">Vec imate nalog?</span>
            <a href="{{ route('login') }}">Prijavite se</a>
        </div>
    </div>
</div>

@endsection
