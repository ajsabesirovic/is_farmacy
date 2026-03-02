@extends('layouts.app')

@section('title', 'Novi recept')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Registracija recepta</h1>
</div>

<form action="{{ route('recepti.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Podaci o receptu</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Broj recepta *</label>
                        <input type="text" class="form-control" name="broj_recepta" required value="{{ old('broj_recepta') }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Datum izdavanja *</label>
                            <input type="date" class="form-control" name="datum_izdavanja" required value="{{ old('datum_izdavanja', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vazi do *</label>
                            <input type="date" class="form-control" name="datum_vazenja" required value="{{ old('datum_vazenja') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sifra dijagnoze</label>
                        <input type="text" class="form-control" name="dijagnoza_sifra" value="{{ old('dijagnoza_sifra') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Napomena</label>
                        <textarea class="form-control" name="napomena" rows="2">{{ old('napomena') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">Podaci o pacijentu i lekaru</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Ime pacijenta</label>
                        <input type="text" class="form-control" name="ime_pacijenta" value="{{ old('ime_pacijenta') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">JMBG pacijenta *</label>
                        <input type="text" class="form-control" name="jmbg_pacijenta" maxlength="13" value="{{ old('jmbg_pacijenta') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lekar *</label>
                        <select class="form-select" name="lekar_id" required>
                            <option value="">Izaberite lekara...</option>
                            @foreach($lekari as $lekar)
                            <option value="{{ $lekar->id }}" {{ old('lekar_id') == $lekar->id ? 'selected' : '' }}>
                                dr {{ $lekar->ime }} {{ $lekar->prezime }} ({{ $lekar->specijalnost ?? 'Bez specijalnosti' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Lekovi na receptu</div>
        <div class="card-body">
            <div id="lekoviContainer">
                <div class="row mb-3 lek-row">
                    <div class="col-md-5">
                        <select class="form-select" name="lekovi[0][lek_id]" required>
                            <option value="">Izaberite lek...</option>
                            @foreach($lekovi as $lek)
                            <option value="{{ $lek->id }}">{{ $lek->naziv }} ({{ $lek->jkl_sifra }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="lekovi[0][kolicina]" placeholder="Kolicina" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="lekovi[0][doziranje]" placeholder="Doziranje (npr. 3x1)">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-lek" disabled><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary" id="addLek">
                <i class="bi bi-plus-lg"></i> Dodaj lek
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-check-lg"></i> Sacuvaj recept
    </button>
    <a href="{{ route('recepti.index') }}" class="btn btn-secondary btn-lg">Odustani</a>
</form>
@endsection

@push('scripts')
<script>
let lekIndex = 1;
document.getElementById('addLek').addEventListener('click', function() {
    const container = document.getElementById('lekoviContainer');
    const row = container.querySelector('.lek-row').cloneNode(true);
    row.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[0\]/, `[${lekIndex}]`);
        el.value = '';
    });
    row.querySelector('.remove-lek').disabled = false;
    container.appendChild(row);
    lekIndex++;
});
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-lek')) {
        e.target.closest('.lek-row').remove();
    }
});
</script>
@endpush
