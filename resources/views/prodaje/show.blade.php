@extends('layouts.app')

@section('title', 'Prodaja - ' . $prodaja->broj_racuna)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Prodaja: {{ $prodaja->broj_racuna }}</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Podaci o prodaji</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">Broj racuna:</th><td><code>{{ $prodaja->broj_racuna }}</code></td></tr>
                    <tr><th>Datum:</th><td>{{ $prodaja->datum?->format('d.m.Y') }} {{ $prodaja->vreme ?? '' }}</td></tr>
                    <tr><th>Apoteka:</th><td>{{ $prodaja->apoteka->naziv }}</td></tr>
                    <tr><th>Farmaceut:</th><td>{{ $prodaja->korisnik->puno_ime }}</td></tr>
                    <tr>
                        <th>Nacin placanja:</th>
                        <td>
                            @if($prodaja->nacin_placanja->value == 'gotovina')
                                <span class="badge bg-success">Gotovina</span>
                            @else
                                <span class="badge bg-primary">Kartica</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @if($prodaja->recept)
        <div class="card mb-4">
            <div class="card-header">Recept</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">Broj recepta:</th><td><code>{{ $prodaja->recept->broj_recepta }}</code></td></tr>
                    <tr><th>Pacijent:</th><td>{{ $prodaja->recept->ime_pacijenta }}</td></tr>
                    <tr><th>Lekar:</th><td>{{ $prodaja->recept->lekar ? $prodaja->recept->lekar->full_name : '-' }}</td></tr>
                </table>
                <a href="{{ route('recepti.show', $prodaja->recept) }}" class="btn btn-sm btn-outline-info">Pogledaj recept</a>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Stavke prodaje</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>#</th><th>Lek</th><th>JKL</th><th>Kolicina</th><th>Cena</th><th>Ukupno</th></tr></thead>
            <tbody>
                @foreach($prodaja->stavke as $index => $stavka)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $stavka->lek->naziv }}</td>
                    <td><code>{{ $stavka->lek->jkl_sifra }}</code></td>
                    <td>{{ $stavka->kolicina }}</td>
                    <td>{{ number_format($stavka->cena_po_komadu, 2) }} RSD</td>
                    <td>{{ number_format($stavka->ukupno, 2) }} RSD</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr><th colspan="5" class="text-end">UKUPNO:</th><th>{{ number_format($prodaja->ukupan_iznos, 2) }} RSD</th></tr>
            </tfoot>
        </table>
    </div>
</div>

<a href="{{ route('prodaje.index') }}" class="btn btn-secondary">Nazad</a>
@endsection
