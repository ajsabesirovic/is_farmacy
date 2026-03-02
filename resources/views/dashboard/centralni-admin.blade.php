@extends('layouts.app')

@section('title', 'Dashboard - Centralni admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard - Centralna administracija</h1>
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
                <h6 class="card-title text-muted">Broj artikala u sistemu</h6>
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
                <i class="bi bi-file-medical"></i> Recepti ovog meseca
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h5>{{ $recept_report['ukupno'] }}</h5>
                        <small class="text-muted">Ukupno</small>
                    </div>
                    <div class="col">
                        <h5 class="text-success">{{ $recept_report['realizovano'] }}</h5>
                        <small class="text-muted">Realizovano</small>
                    </div>
                    <div class="col">
                        <h5 class="text-warning">{{ $recept_report['ceka_realizaciju'] }}</h5>
                        <small class="text-muted">Ceka realizaciju</small>
                    </div>
                    <div class="col">
                        <h5 class="text-danger">{{ $recept_report['isteklo'] }}</h5>
                        <small class="text-muted">Isteklo</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-star"></i> Top 10 najtrazenijih lekova
            </div>
            <ul class="list-group list-group-flush">
                @forelse($najtrazeniji_lekovi as $index => $lek)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $index + 1 }}. {{ $lek['naziv'] }}</span>
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
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <span><i class="bi bi-exclamation-triangle"></i> Lekovi sa niskim zalihama (sve apoteke)</span>
        <a href="{{ route('reports.zalihe') }}" class="btn btn-sm btn-outline-dark">Detaljan izvestaj</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Apoteka</th>
                    <th>Lek</th>
                    <th>Trenutno</th>
                    <th>Minimum</th>
                </tr>
            </thead>
            <tbody>
                @foreach($niske_zalihe->take(10) as $zaliha)
                <tr>
                    <td>{{ $zaliha->apoteka->naziv }}</td>
                    <td>{{ $zaliha->lek->naziv }}</td>
                    <td class="text-danger fw-bold">{{ $zaliha->kolicina }}</td>
                    <td>{{ $zaliha->min_zaliha }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
