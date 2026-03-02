@extends('layouts.app')

@section('title', 'Izvestaj - Najtrazeniji lekovi')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Najtrazeniji lekovi</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('reports.lekovi') }}" method="GET" class="row g-3">
            @if(Auth::user()->isCentralniAdmin())
            <div class="col-md-3">
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
            @endif
            <div class="col-md-2">
                <label class="form-label">Od datuma</label>
                <input type="date" class="form-control" name="od" value="{{ $od }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Do datuma</label>
                <input type="date" class="form-control" name="do" value="{{ $do }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Broj rezultata</label>
                <select class="form-select" name="limit">
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Prikazi</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Top {{ $limit }} najtrazenijih lekova</div>
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Naziv</th>
                    <th>Proizvodjac</th>
                    <th>JKL Sifra</th>
                    <th>Ukupno prodato</th>
                    <th>Broj prodaja</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $index => $item)
                <tr>
                    <td><strong>{{ $index + 1 }}</strong></td>
                    <td>{{ $item['naziv'] }}</td>
                    <td>{{ $item['proizvodjac'] }}</td>
                    <td><code>{{ $item['jkl_sifra'] }}</code></td>
                    <td><span class="badge bg-primary">{{ $item['ukupno_prodato'] }}</span></td>
                    <td>{{ $item['broj_prodaja'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Nema podataka za izabrani period</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
