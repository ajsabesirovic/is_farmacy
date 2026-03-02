@extends('layouts.app')

@section('title', 'Dashboard - Admin apoteke')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard - {{ Auth::user()->apoteka->naziv }}</h1>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-stat primary">
            <div class="card-body">
                <h6 class="card-title text-muted">Ukupna vrednost zaliha</h6>
                <h4 class="mb-0">{{ number_format($inventar_summary['ukupna_vrednost'], 2) }} RSD</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stat success">
            <div class="card-body">
                <h6 class="card-title text-muted">Broj artikala</h6>
                <h4 class="mb-0">{{ $inventar_summary['broj_artikala'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stat warning">
            <div class="card-body">
                <h6 class="card-title text-muted">Niske zalihe</h6>
                <h4 class="mb-0">{{ $inventar_summary['niske_zalihe'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card card-stat danger">
            <div class="card-body">
                <h6 class="card-title text-muted">Bez zaliha</h6>
                <h4 class="mb-0">{{ $inventar_summary['bez_zaliha'] }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> Prodaja ovog meseca
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h5>{{ $mesecna_prodaja['ukupno_prodaja'] }}</h5>
                        <small class="text-muted">Broj prodaja</small>
                    </div>
                    <div class="col">
                        <h5>{{ number_format($mesecna_prodaja['ukupan_promet'], 2) }} RSD</h5>
                        <small class="text-muted">Ukupan promet</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-star"></i> Najtrazeniji lekovi
            </div>
            <ul class="list-group list-group-flush">
                @forelse($najtrazeniji_lekovi as $lek)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $lek['naziv'] }}
                    <span class="badge bg-primary rounded-pill">{{ $lek['ukupno_prodato'] }}</span>
                </li>
                @empty
                <li class="list-group-item text-muted">Nema podataka</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@if($niske_zalihe->isNotEmpty())
<div class="card">
    <div class="card-header bg-warning text-dark">
        <i class="bi bi-exclamation-triangle"></i> Lekovi sa niskim zalihama
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Lek</th>
                    <th>JKL Sifra</th>
                    <th>Trenutno</th>
                    <th>Minimum</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                @foreach($niske_zalihe as $zaliha)
                <tr>
                    <td>{{ $zaliha->lek->naziv }}</td>
                    <td>{{ $zaliha->lek->jkl_sifra }}</td>
                    <td class="text-danger fw-bold">{{ $zaliha->kolicina }}</td>
                    <td>{{ $zaliha->min_zaliha }}</td>
                    <td>
                        <a href="{{ route('narudzbenice.create', ['lek_id' => $zaliha->lek_id]) }}" class="btn btn-sm btn-outline-primary">
                            Naruci
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
