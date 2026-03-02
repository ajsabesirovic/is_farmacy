@extends('layouts.app')

@section('title', 'Izvestaj - Zalihe')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Izvestaj o zalihama</h1>
</div>

@if(Auth::user()->isCentralniAdmin())
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reports.zalihe') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Apoteka</label>
                <select class="form-select" name="apoteka_id">
                    <option value="">Sve apoteke</option>
                    @foreach($apoteke as $apoteka)
                        <option value="{{ $apoteka->id }}" {{ $apotekaId == $apoteka->id ? 'selected' : '' }}>
                            {{ $apoteka->naziv }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filtriraj</button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-stat primary">
            <div class="card-body">
                <h6 class="text-muted">Ukupna vrednost</h6>
                <h3>{{ number_format($summary['ukupna_vrednost'], 2) }} RSD</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat success">
            <div class="card-body">
                <h6 class="text-muted">Broj artikala</h6>
                <h3>{{ $summary['broj_artikala'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat warning">
            <div class="card-body">
                <h6 class="text-muted">Niske zalihe</h6>
                <h3>{{ $summary['niske_zalihe'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat danger">
            <div class="card-body">
                <h6 class="text-muted">Bez zaliha</h6>
                <h3>{{ $summary['bez_zaliha'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Detaljan pregled zaliha</div>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    @if(Auth::user()->isCentralniAdmin())<th>Apoteka</th>@endif
                    <th>Lek</th>
                    <th>JKL</th>
                    <th>Kolicina</th>
                    <th>Min</th>
                    <th>Cena</th>
                    <th>Vrednost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $item)
                <tr class="{{ $item['status'] == 'NISKA_ZALIHA' ? 'table-warning' : ($item['status'] == 'NEMA' ? 'table-danger' : '') }}">
                    @if(Auth::user()->isCentralniAdmin())<td>{{ $item['apoteka'] }}</td>@endif
                    <td>{{ $item['lek'] }}</td>
                    <td><code>{{ $item['jkl_sifra'] }}</code></td>
                    <td>{{ $item['kolicina'] }}</td>
                    <td>{{ $item['min_zaliha'] }}</td>
                    <td>{{ number_format($item['cena'], 2) }}</td>
                    <td>{{ number_format($item['vrednost'], 2) }}</td>
                    <td><span class="badge bg-{{ $item['status'] == 'OK' ? 'success' : ($item['status'] == 'NISKA_ZALIHA' ? 'warning' : 'danger') }}">{{ $item['status'] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
