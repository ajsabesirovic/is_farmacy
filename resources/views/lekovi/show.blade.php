@extends('layouts.app')

@section('title', 'Lek - ' . $lek->naziv)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $lek->naziv }}</h1>
    @if($lek->na_recept)
        <span class="badge bg-danger fs-6">Na recept</span>
    @else
        <span class="badge bg-success fs-6">Bez recepta</span>
    @endif
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Podaci o leku</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">JKL Sifra:</th><td><code>{{ $lek->jkl_sifra }}</code></td></tr>
                    <tr><th>Proizvodjac:</th><td>{{ $lek->proizvodjac }}</td></tr>
                    <tr><th>Oblik:</th><td>{{ $lek->farm_oblik ?? '-' }}</td></tr>
                    <tr><th>Jacina:</th><td>{{ $lek->jacina ?? '-' }}</td></tr>
                    <tr><th>Pakovanje:</th><td>{{ $lek->pakovanje ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">{{ Auth::user()->isCentralniAdmin() ? 'Dostupnost u apotekama' : 'Zalihe u vasoj apoteci' }}</div>
            <ul class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                @forelse($lek->zalihe as $zaliha)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $zaliha->apoteka->naziv }}</span>
                    <span>
                        <strong>{{ $zaliha->kolicina }}</strong> kom
                        - {{ number_format($zaliha->prodajna_cena, 2) }} RSD
                    </span>
                </li>
                @empty
                <li class="list-group-item text-muted">Nema na zalihama</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Dobavljaci</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Dobavljac</th><th>Nabavna cena</th></tr></thead>
            <tbody>
                @forelse($lek->dobavljaci as $dobavljac)
                <tr>
                    <td>{{ $dobavljac->naziv }}</td>
                    <td>{{ number_format($dobavljac->pivot->nabavna_cena, 2) }} RSD</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-muted">Nema dobavljaca</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<a href="{{ route('lekovi.index') }}" class="btn btn-secondary">Nazad</a>
@if(Auth::user()->isCentralniAdmin())
<a href="{{ route('lekovi.edit', $lek) }}" class="btn btn-primary">Izmeni</a>
@endif
@endsection
