@extends('layouts.app')

@section('title', 'Izvestaj - Prodaja')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Izvestaj o prodaji</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reports.prodaja') }}" method="GET" class="row g-3">
            @if(Auth::user()->isCentralniAdmin())
            <div class="col-md-3">
                <label class="form-label">Apoteka</label>
                <select class="form-select" name="apoteka_id">
                    <option value="">Izaberite apoteku</option>
                    @foreach($apoteke as $apoteka)
                        <option value="{{ $apoteka->id }}" {{ $apotekaId == $apoteka->id ? 'selected' : '' }}>
                            {{ $apoteka->naziv }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3">
                <label class="form-label">Od datuma</label>
                <input type="date" class="form-control" name="od" value="{{ $od }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Do datuma</label>
                <input type="date" class="form-control" name="do" value="{{ $do }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Prikazi izvestaj
                </button>
            </div>
        </form>
    </div>
</div>

@if($report)
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-stat primary">
            <div class="card-body">
                <h6 class="text-muted">Ukupno prodaja</h6>
                <h3>{{ $report['ukupno_prodaja'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat success">
            <div class="card-body">
                <h6 class="text-muted">Ukupan promet</h6>
                <h3>{{ number_format($report['ukupan_promet'], 2) }} RSD</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat warning">
            <div class="card-body">
                <h6 class="text-muted">Prosecna vrednost</h6>
                <h3>{{ number_format($report['prosecna_vrednost'], 2) }} RSD</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Po nacinu placanja</div>
            <ul class="list-group list-group-flush">
                @foreach($report['po_nacinu_placanja'] as $nacin => $podaci)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ ucfirst($nacin) }}</span>
                    <span>{{ $podaci['broj'] }} prodaja ({{ number_format($podaci['iznos'], 2) }} RSD)</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Dnevni pregled</div>
            <div class="table-responsive" style="max-height: 300px;">
                <table class="table table-sm">
                    <thead>
                        <tr><th>Datum</th><th>Broj</th><th>Iznos</th></tr>
                    </thead>
                    <tbody>
                        @foreach($report['dnevni_pregled'] as $datum => $podaci)
                        <tr>
                            <td>{{ $datum }}</td>
                            <td>{{ $podaci['broj_prodaja'] }}</td>
                            <td>{{ number_format($podaci['ukupan_iznos'], 2) }} RSD</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">Izaberite apoteku i period za prikaz izvestaja.</div>
@endif
@endsection
