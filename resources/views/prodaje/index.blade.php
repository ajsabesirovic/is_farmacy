@extends('layouts.app')

@section('title', 'Prodaje')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Prodaje</h1>
    <a href="{{ route('prodaje.create') }}" class="btn btn-primary">
        <i class="bi bi-cart-plus"></i> Nova prodaja
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('prodaje.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Od datuma</label>
                <input type="date" class="form-control" name="datum_od" value="{{ request('datum_od') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Do datuma</label>
                <input type="date" class="form-control" name="datum_do" value="{{ request('datum_do') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-filter"></i> Filtriraj
                </button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Datum</th>
                <th>Vreme</th>
                <th>Prodavac</th>
                <th>Broj stavki</th>
                <th>Ukupno</th>
                <th>Placanje</th>
                <th>Recept</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prodaje as $prodaja)
            <tr>
                <td>#{{ $prodaja->id }}</td>
                <td>{{ $prodaja->datum->format('d.m.Y') }}</td>
                <td>{{ $prodaja->vreme }}</td>
                <td>{{ $prodaja->korisnik->puno_ime }}</td>
                <td>{{ $prodaja->stavke->count() }}</td>
                <td><strong>{{ number_format($prodaja->ukupan_iznos, 2) }} RSD</strong></td>
                <td>
                    <span class="badge bg-secondary">{{ $prodaja->nacin_placanja->label() }}</span>
                </td>
                <td>
                    @if($prodaja->recept)
                        <a href="{{ route('recepti.show', $prodaja->recept) }}">
                            {{ $prodaja->recept->broj_recepta }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    <a href="{{ route('prodaje.show', $prodaja) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i> Detalji
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted">Nema prodaja</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $prodaje->links() }}
</div>
@endsection
