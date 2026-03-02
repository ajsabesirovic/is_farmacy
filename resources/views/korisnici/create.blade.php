@extends('layouts.app')

@section('title', 'Novi korisnik')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Novi korisnik</h1>
</div>

<form action="{{ route('korisnici.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Licni podaci</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ime *</label>
                        <input type="text" class="form-control @error('ime') is-invalid @enderror" name="ime" value="{{ old('ime') }}" required>
                        @error('ime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prezime *</label>
                        <input type="text" class="form-control @error('prezime') is-invalid @enderror" name="prezime" value="{{ old('prezime') }}" required>
                        @error('prezime')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lozinka *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Potvrda lozinke *</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Uloga</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tip korisnika *</label>
                        <select class="form-select @error('tip') is-invalid @enderror" name="tip" id="tipKorisnika" required>
                            <option value="">Izaberite...</option>
                            <option value="F" {{ old('tip') == 'F' ? 'selected' : '' }}>Farmaceut</option>
                            <option value="A" {{ old('tip') == 'A' ? 'selected' : '' }}>Admin apoteke</option>
                            @if(Auth::user()->isCentralniAdmin())
                            <option value="C" {{ old('tip') == 'C' ? 'selected' : '' }}>Centralni admin</option>
                            <option value="R" {{ old('tip') == 'R' ? 'selected' : '' }}>Registrovani korisnik</option>
                            @endif
                        </select>
                        @error('tip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3" id="apotekaField">
                        <label class="form-label">Apoteka *</label>
                        <select class="form-select @error('apoteka_id') is-invalid @enderror" name="apoteka_id">
                            <option value="">Izaberite apoteku...</option>
                            @foreach($apoteke ?? [] as $apoteka)
                            <option value="{{ $apoteka->id }}" {{ old('apoteka_id') == $apoteka->id ? 'selected' : '' }}>{{ $apoteka->naziv }}</option>
                            @endforeach
                        </select>
                        @error('apoteka_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3" id="licencaField" style="display: none;">
                        <label class="form-label">Broj licence</label>
                        <input type="text" class="form-control @error('licenca') is-invalid @enderror" name="licenca" value="{{ old('licenca') }}">
                        @error('licenca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3" id="jmbgField" style="display: none;">
                        <label class="form-label">JMBG *</label>
                        <input type="text" class="form-control @error('jmbg') is-invalid @enderror" name="jmbg" value="{{ old('jmbg') }}" maxlength="13">
                        @error('jmbg')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-lg"></i> Sacuvaj</button>
    <a href="{{ route('korisnici.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>

@push('scripts')
<script>
document.getElementById('tipKorisnika').addEventListener('change', function() {
    const apotekaField = document.getElementById('apotekaField');
    const licencaField = document.getElementById('licencaField');
    const jmbgField = document.getElementById('jmbgField');

    if (this.value === 'C' || this.value === 'R') {
        apotekaField.style.display = 'none';
    } else {
        apotekaField.style.display = 'block';
    }
    licencaField.style.display = this.value === 'F' ? 'block' : 'none';
    jmbgField.style.display = this.value === 'R' ? 'block' : 'none';
});
document.getElementById('tipKorisnika').dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
