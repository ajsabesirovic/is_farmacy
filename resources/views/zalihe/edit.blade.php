@extends('layouts.app')

@section('title', 'Izmena zalihe')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Izmena zalihe: {{ $zaliha->lek->naziv }}</h1>
</div>

<form action="{{ route('zalihe.update', [$zaliha->apoteka_id, $zaliha->lek_id]) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Informacije</div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="35%">Apoteka:</th><td>{{ $zaliha->apoteka->naziv }}</td></tr>
                        <tr><th>Lek:</th><td>{{ $zaliha->lek->naziv }}</td></tr>
                        <tr><th>JKL Sifra:</th><td><code>{{ $zaliha->lek->jkl_sifra }}</code></td></tr>
                        <tr><th>Proizvodjac:</th><td>{{ $zaliha->lek->proizvodjac }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Zalihe</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kolicina *</label>
                        <input type="number" class="form-control @error('kolicina') is-invalid @enderror" name="kolicina" value="{{ old('kolicina', $zaliha->kolicina) }}" min="0" required>
                        @error('kolicina')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimalna zaliha</label>
                        <input type="number" class="form-control @error('min_zaliha') is-invalid @enderror" name="min_zaliha" value="{{ old('min_zaliha', $zaliha->min_zaliha) }}" min="0">
                        @error('min_zaliha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prodajna cena (RSD) *</label>
                        <input type="number" step="0.01" class="form-control @error('prodajna_cena') is-invalid @enderror" name="prodajna_cena" value="{{ old('prodajna_cena', $zaliha->prodajna_cena) }}" min="0" required>
                        @error('prodajna_cena')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg"></i> Sacuvaj izmene</button>
    <a href="{{ route('zalihe.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>
@endsection
