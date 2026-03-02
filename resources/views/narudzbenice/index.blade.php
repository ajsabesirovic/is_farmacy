@extends('layouts.app')

@section('title', 'Narudzbenice')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Narudzbenice</h1>
    <a href="{{ route('narudzbenice.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nova narudzbenica
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('narudzbenice.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="">Svi statusi</option>
                    <option value="nacrt" {{ request('status') == 'nacrt' ? 'selected' : '' }}>Nacrt</option>
                    <option value="poslata" {{ request('status') == 'poslata' ? 'selected' : '' }}>Poslata</option>
                    <option value="isporucena" {{ request('status') == 'isporucena' ? 'selected' : '' }}>Isporucena</option>
                    <option value="otkazana" {{ request('status') == 'otkazana' ? 'selected' : '' }}>Otkazana</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary">Filtriraj</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Broj</th>
                <th>Datum</th>
                <th>Dobavljac</th>
                @if(Auth::user()->isCentralniAdmin())<th>Apoteka</th>@endif
                <th>Vrednost</th>
                <th>Status</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            @forelse($narudzbenice as $narudzbenica)
            <tr>
                <td><code>{{ $narudzbenica->broj_narudzbenice }}</code></td>
                <td>{{ $narudzbenica->datum_kreiranja->format('d.m.Y') }}</td>
                <td>{{ $narudzbenica->dobavljac->naziv }}</td>
                @if(Auth::user()->isCentralniAdmin())<td>{{ $narudzbenica->apoteka->naziv }}</td>@endif
                <td>{{ number_format($narudzbenica->ukupna_vrednost, 2) }} RSD</td>
                <td><span class="badge bg-{{ $narudzbenica->status->color() }}">{{ $narudzbenica->status->label() }}</span></td>
                <td>
                    <a href="{{ route('narudzbenice.show', $narudzbenica) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Nema narudzbenica</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $narudzbenice->links() }}
</div>
@endsection
