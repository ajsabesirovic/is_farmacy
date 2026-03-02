@extends('layouts.app')

@section('title', 'Recepti')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Recepti</h1>
    <a href="{{ route('recepti.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Novi recept
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('recepti.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Broj recepta, ime pacijenta..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">Svi statusi</option>
                    <option value="izdat" {{ request('status') == 'izdat' ? 'selected' : '' }}>Izdat</option>
                    <option value="realizovan" {{ request('status') == 'realizovan' ? 'selected' : '' }}>Realizovan</option>
                    <option value="istekao" {{ request('status') == 'istekao' ? 'selected' : '' }}>Istekao</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Pretrazi</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Broj recepta</th>
                <th>Pacijent</th>
                <th>Lekar</th>
                <th>Datum izdavanja</th>
                <th>Vazi do</th>
                <th>Status</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recepti as $recept)
            <tr>
                <td><code>{{ $recept->broj_recepta }}</code></td>
                <td>{{ $recept->ime_pacijenta ?? '-' }}</td>
                <td>{{ $recept->lekar ? $recept->lekar->full_name : '-' }}</td>
                <td>{{ $recept->datum_izdavanja->format('d.m.Y') }}</td>
                <td>{{ $recept->datum_vazenja->format('d.m.Y') }}</td>
                <td><span class="badge bg-{{ $recept->status->color() }}">{{ $recept->status->label() }}</span></td>
                <td>
                    <a href="{{ route('recepti.show', $recept) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">Nema recepata</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $recepti->links() }}
</div>
@endsection
