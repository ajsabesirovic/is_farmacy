@extends('layouts.app')

@section('title', 'Izvestaj - Dobavljaci')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Izvestaj o dobavljacima</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reports.dobavljaci') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Dobavljac</label>
                <select class="form-select" name="dobavljac_id">
                    <option value="">Svi dobavljaci</option>
                    @foreach($dobavljaci as $dobavljac)
                    <option value="{{ $dobavljac->id }}" {{ $dobavljacId == $dobavljac->id ? 'selected' : '' }}>{{ $dobavljac->naziv }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Od datuma</label>
                <input type="date" class="form-control" name="od" value="{{ $od }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Do datuma</label>
                <input type="date" class="form-control" name="do" value="{{ $do }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Primeni filter</button>
            </div>
        </form>
    </div>
</div>

@if($report && $report->count() > 0)
<div class="card mb-4">
    <div class="card-header">Pregled narudzbi po dobavljacima</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Dobavljac</th>
                    <th>Ukupno narudzbi</th>
                    <th>Isporuceno</th>
                    <th>U toku</th>
                    <th>Otkazano</th>
                    <th>Ukupna vrednost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report as $item)
                <tr>
                    <td><strong>{{ $item['naziv'] }}</strong></td>
                    <td>{{ $item['ukupno_narudzbi'] }}</td>
                    <td><span class="badge bg-success">{{ $item['isporuceno'] }}</span></td>
                    <td><span class="badge bg-warning text-dark">{{ $item['u_toku'] }}</span></td>
                    <td><span class="badge bg-danger">{{ $item['otkazano'] }}</span></td>
                    <td><strong>{{ number_format($item['ukupna_vrednost'], 2) }} RSD</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <th>UKUPNO</th>
                    <th>{{ $report->sum('ukupno_narudzbi') }}</th>
                    <th>{{ $report->sum('isporuceno') }}</th>
                    <th>{{ $report->sum('u_toku') }}</th>
                    <th>{{ $report->sum('otkazano') }}</th>
                    <th>{{ number_format($report->sum('ukupna_vrednost'), 2) }} RSD</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Statistika</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th>Ukupno narudzbi:</th><td>{{ $report->sum('ukupno_narudzbi') }}</td></tr>
                    <tr><th>Uspesno isporuceno:</th><td>{{ $report->sum('isporuceno') }} ({{ $report->sum('ukupno_narudzbi') > 0 ? number_format(($report->sum('isporuceno') / $report->sum('ukupno_narudzbi')) * 100, 1) : 0 }}%)</td></tr>
                    <tr><th>Ukupna vrednost:</th><td><strong>{{ number_format($report->sum('ukupna_vrednost'), 2) }} RSD</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">Nema podataka za prikaz za izabrani period.</div>
@endif
@endsection
