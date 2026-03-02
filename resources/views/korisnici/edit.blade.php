@extends('layouts.app')

@section('title', 'Izmena korisnika')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Izmena korisnika: {{ $korisnik->puno_ime }}</h1>
</div>

<form action="{{ route('korisnici.update', $korisnik) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Licni podaci</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ime *</label>
                        <input type="text" class="form-control @error('ime') is-invalid @enderror" name="ime" value="{{ old('ime', $korisnik->ime) }}" required>
                        @error('ime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prezime *</label>
                        <input type="text" class="form-control @error('prezime') is-invalid @enderror" name="prezime" value="{{ old('prezime', $korisnik->prezime) }}" required>
                        @error('prezime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $korisnik->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nova lozinka</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Ostavite prazno ako ne menjate" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potvrda lozinke</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Ostavite prazno ako ne menjate" autocomplete="new-password">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Uloga</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tip korisnika</label>
                        <input type="text" class="form-control" value="{{ $korisnik->tip->label() }}" readonly disabled>
                    </div>
                    @if($korisnik->apoteka)
                    <div class="mb-3">
                        <label class="form-label">Apoteka</label>
                        <input type="text" class="form-control" value="{{ $korisnik->apoteka->naziv }}" readonly disabled>
                    </div>
                    @endif
                    @if($korisnik->isFarmaceut() && $korisnik->farmaceut)
                    <div class="mb-3">
                        <label class="form-label">Broj licence</label>
                        <input type="text" class="form-control @error('licenca') is-invalid @enderror" name="licenca" value="{{ old('licenca', $korisnik->farmaceut->licenca) }}">
                        @error('licenca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    @endif
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktivan" value="1" id="aktivan" {{ old('aktivan', $korisnik->aktivan) ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktivan">Aktivan nalog</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg"></i> Sacuvaj izmene</button>
    <a href="{{ route('korisnici.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>
@endsection
