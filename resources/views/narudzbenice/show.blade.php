@extends('layouts.app')

@section('title', 'Narudzbenica - ' . $narudzbenica->broj_narudzbenice)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Narudzbenica: {{ $narudzbenica->broj_narudzbenice }}</h1>
    <span class="badge bg-{{ $narudzbenica->status->color() }} fs-6">{{ $narudzbenica->status->label() }}</span>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Podaci o narudzbenici</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Broj:</th><td><code>{{ $narudzbenica->broj_narudzbenice }}</code></td></tr>
                    <tr><th>Datum kreiranja:</th><td>{{ $narudzbenica->datum_kreiranja->format('d.m.Y H:i') }}</td></tr>
                    <tr><th>Datum isporuke:</th><td>{{ $narudzbenica->datum_isporuke?->format('d.m.Y') ?? '-' }}</td></tr>
                    <tr><th>Kreirao:</th><td>{{ $narudzbenica->korisnik->puno_ime }}</td></tr>
                    <tr><th>Napomena:</th><td>{{ $narudzbenica->napomena ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Dobavljac</div>
            <div class="card-body">
                <h5>{{ $narudzbenica->dobavljac->naziv }}</h5>
                <p class="mb-1"><i class="bi bi-telephone"></i> {{ $narudzbenica->dobavljac->telefon }}</p>
                <p class="mb-0"><i class="bi bi-envelope"></i> {{ $narudzbenica->dobavljac->email }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Stavke narudzbenice</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Lek</th><th>JKL</th><th>Kolicina</th><th>Cena</th><th>Ukupno</th></tr></thead>
            <tbody>
                @foreach($narudzbenica->stavke as $stavka)
                <tr>
                    <td>{{ $stavka->redni_broj }}</td>
                    <td>{{ $stavka->lek->naziv }}</td>
                    <td><code>{{ $stavka->lek->jkl_sifra }}</code></td>
                    <td>{{ $stavka->kolicina }}</td>
                    <td>{{ number_format($stavka->cena_po_komadu, 2) }} RSD</td>
                    <td>{{ number_format($stavka->ukupno, 2) }} RSD</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="5" class="text-end">UKUPNO:</th><th>{{ number_format($narudzbenica->ukupna_vrednost, 2) }} RSD</th></tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mb-4">
    @if($narudzbenica->status->value == 'nacrt')
        <form action="{{ route('narudzbenice.status', $narudzbenica) }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="status" value="poslata">
            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Posalji</button>
        </form>
    @endif
    @if($narudzbenica->status->value == 'poslata')
        <form action="{{ route('narudzbenice.isporuceno', $narudzbenica) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success"><i class="bi bi-truck"></i> Oznaci kao isporuceno</button>
        </form>
    @endif
    @if(!in_array($narudzbenica->status->value, ['isporucena', 'otkazana']))
        <form action="{{ route('narudzbenice.otkazi', $narudzbenica) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger" onclick="return confirm('Sigurno zelite da otkazete?')"><i class="bi bi-x"></i> Otkazi</button>
        </form>
    @endif
</div>

<a href="{{ route('narudzbenice.index') }}" class="btn btn-secondary">Nazad</a>
@endsection
