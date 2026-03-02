@extends('layouts.app')

@section('title', 'Novi lek')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Novi lek</h1>
</div>

<form action="{{ route('lekovi.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Osnovni podaci</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">JKL Sifra *</label>
                        <input type="text" class="form-control @error('jkl_sifra') is-invalid @enderror" name="jkl_sifra" value="{{ old('jkl_sifra') }}" required>
                        @error('jkl_sifra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Naziv *</label>
                        <input type="text" class="form-control @error('naziv') is-invalid @enderror" name="naziv" value="{{ old('naziv') }}" required>
                        @error('naziv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Proizvodjac *</label>
                        <input type="text" class="form-control @error('proizvodjac') is-invalid @enderror" name="proizvodjac" value="{{ old('proizvodjac') }}" required>
                        @error('proizvodjac')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Farmaceutski oblik</label>
                        <input type="text" class="form-control @error('farm_oblik') is-invalid @enderror" name="farm_oblik" value="{{ old('farm_oblik') }}" placeholder="npr. tablete, kapsule, sirup">
                        @error('farm_oblik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Dodatni podaci</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Jacina</label>
                        <input type="text" class="form-control @error('jacina') is-invalid @enderror" name="jacina" value="{{ old('jacina') }}" placeholder="npr. 500mg">
                        @error('jacina')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pakovanje</label>
                        <input type="text" class="form-control @error('pakovanje') is-invalid @enderror" name="pakovanje" value="{{ old('pakovanje') }}" placeholder="npr. 20 tableta">
                        @error('pakovanje')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="na_recept" value="1" id="naRecept" {{ old('na_recept') ? 'checked' : '' }}>
                        <label class="form-check-label" for="naRecept">Izdaje se na recept</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-lg"></i> Sacuvaj</button>
    <a href="{{ route('lekovi.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>
@endsection
