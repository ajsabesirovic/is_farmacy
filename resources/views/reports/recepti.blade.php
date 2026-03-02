@extends('layouts.app')

@section('title', 'Izvestaj - Recepti')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Izvestaj o receptima</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reports.recepti') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Od datuma</label>
                <input type="date" class="form-control" name="od" value="{{ $od }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Do datuma</label>
                <input type="date" class="form-control" name="do" value="{{ $do }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Primeni filter</button>
            </div>
        </form>
    </div>
</div>

@if($report)
<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body text-center">
                <h3>{{ $report['ukupno'] }}</h3>
                <p class="mb-0">Ukupno recepata</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white mb-4">
            <div class="card-body text-center">
                <h3>{{ $report['realizovano'] }}</h3>
                <p class="mb-0">Realizovano</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark mb-4">
            <div class="card-body text-center">
                <h3>{{ $report['ceka_realizaciju'] }}</h3>
                <p class="mb-0">Ceka realizaciju</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white mb-4">
            <div class="card-body text-center">
                <h3>{{ $report['isteklo'] }}</h3>
                <p class="mb-0">Isteklo</p>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Pregled po statusu</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Status</th><th>Broj recepata</th><th>Procenat</th></tr></thead>
            <tbody>
                @foreach($report['po_statusu'] as $status => $broj)
                <tr>
                    <td>
                        @switch($status)
                            @case('izdat')
                                <span class="badge bg-info">Izdat</span>
                                @break
                            @case('validan')
                                <span class="badge bg-primary">Validan</span>
                                @break
                            @case('realizovan')
                                <span class="badge bg-success">Realizovan</span>
                                @break
                            @case('istekao')
                                <span class="badge bg-danger">Istekao</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ $status }}</span>
                        @endswitch
                    </td>
                    <td>{{ $broj }}</td>
                    <td>{{ $report['ukupno'] > 0 ? number_format(($broj / $report['ukupno']) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">Nema podataka za prikaz. Izaberite period.</div>
@endif
@endsection
