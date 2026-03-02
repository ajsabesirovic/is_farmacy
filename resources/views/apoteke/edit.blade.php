@extends('layouts.app')

@section('title', 'Izmena apoteke')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Izmena apoteke: {{ $apoteka->naziv }}</h1>
</div>

<form action="{{ route('apoteke.update', $apoteka) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Osnovni podaci</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Naziv *</label>
                        <input type="text" class="form-control @error('naziv') is-invalid @enderror" name="naziv" value="{{ old('naziv', $apoteka->naziv) }}" required>
                        @error('naziv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grad *</label>
                        <input type="text" class="form-control @error('grad') is-invalid @enderror" name="grad" value="{{ old('grad', $apoteka->grad) }}" required>
                        @error('grad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresa *</label>
                        <input type="text" class="form-control @error('adresa') is-invalid @enderror" name="adresa" value="{{ old('adresa', $apoteka->adresa) }}" required>
                        @error('adresa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Kontakt podaci</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" class="form-control @error('telefon') is-invalid @enderror" name="telefon" value="{{ old('telefon', $apoteka->telefon) }}">
                        @error('telefon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $apoteka->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg"></i> Sacuvaj izmene</button>
    <a href="{{ route('apoteke.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>
@endsection
