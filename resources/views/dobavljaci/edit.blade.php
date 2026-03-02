@extends('layouts.app')

@section('title', 'Izmena dobavljaca')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Izmena dobavljaca: {{ $dobavljac->naziv }}</h1>
</div>

<form action="{{ route('dobavljaci.update', $dobavljac) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Osnovni podaci</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Naziv *</label>
                        <input type="text" class="form-control @error('naziv') is-invalid @enderror" name="naziv" value="{{ old('naziv', $dobavljac->naziv) }}" required>
                        @error('naziv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PIB *</label>
                        <input type="text" class="form-control @error('pib') is-invalid @enderror" name="pib" value="{{ old('pib', $dobavljac->pib) }}" required>
                        @error('pib')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        <input type="text" class="form-control @error('telefon') is-invalid @enderror" name="telefon" value="{{ old('telefon', $dobavljac->telefon) }}">
                        @error('telefon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $dobavljac->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="aktivan" value="0">
                        <input class="form-check-input" type="checkbox" name="aktivan" value="1" id="aktivan" {{ old('aktivan', $dobavljac->aktivan) ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktivan">Aktivan dobavljac</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg"></i> Sacuvaj izmene</button>
    <a href="{{ route('dobavljaci.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>
@endsection
