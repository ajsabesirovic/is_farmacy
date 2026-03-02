@extends('layouts.app')

@section('title', 'Recept - ' . $recept->broj_recepta)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Recept: {{ $recept->broj_recepta }}</h1>
    <span class="badge bg-{{ $recept->status->color() }} fs-6">{{ $recept->status->label() }}</span>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Podaci o receptu</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="40%">Broj recepta:</th><td><code>{{ $recept->broj_recepta }}</code></td></tr>
                    <tr><th>Datum izdavanja:</th><td>{{ $recept->datum_izdavanja->format('d.m.Y') }}</td></tr>
                    <tr><th>Vazi do:</th><td>{{ $recept->datum_vazenja->format('d.m.Y') }}</td></tr>
                    <tr><th>Sifra dijagnoze:</th><td>{{ $recept->dijagnoza_sifra ?? '-' }}</td></tr>
                    <tr><th>Napomena:</th><td>{{ $recept->napomena ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Pacijent i lekar</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="40%">Pacijent:</th><td>{{ $recept->ime_pacijenta ?? '-' }}</td></tr>
                    <tr><th>JMBG:</th><td>{{ $recept->jmbg_pacijenta ?? '-' }}</td></tr>
                    <tr><th>Lekar:</th><td>{{ $recept->lekar ? $recept->lekar->full_name . ' (' . $recept->lekar->specijalnost . ')' : '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Lekovi na receptu</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr><th>Lek</th><th>JKL</th><th>Kolicina</th><th>Doziranje</th></tr>
            </thead>
            <tbody>
                @foreach($recept->lekovi as $lek)
                <tr>
                    <td>{{ $lek->naziv }}</td>
                    <td><code>{{ $lek->jkl_sifra }}</code></td>
                    <td>{{ $lek->pivot->kolicina }}</td>
                    <td>{{ $lek->pivot->doziranje ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($recept->prodaja)
<div class="alert alert-success">
    <i class="bi bi-check-circle"></i> Realizovano prodajom #{{ $recept->prodaja->id }} dana {{ $recept->prodaja->datum->format('d.m.Y') }}
</div>
@elseif($recept->isValid())
<a href="{{ route('prodaje.create', ['recept' => $recept->id]) }}" class="btn btn-success">
    <i class="bi bi-cart"></i> Realizuj recept
</a>
@endif

<a href="{{ route('recepti.index') }}" class="btn btn-secondary">Nazad</a>
@endsection
