@extends('layouts.app')

@section('title', 'Lekovi')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Lekovi</h1>
    @if(Auth::user()->isCentralniAdmin())
    <a href="{{ route('lekovi.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Dodaj lek
    </a>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('lekovi.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Pretrazi po nazivu, JKL sifri ili proizvodjacu..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="na_recept">
                    <option value="">Svi lekovi</option>
                    <option value="1" {{ request('na_recept') === '1' ? 'selected' : '' }}>Na recept</option>
                    <option value="0" {{ request('na_recept') === '0' ? 'selected' : '' }}>Bez recepta</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Pretrazi
                </button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Naziv</th>
                <th>JKL Sifra</th>
                <th>Proizvodjac</th>
                <th>Oblik</th>
                <th>Jacina</th>
                <th>Rezim</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lekovi as $lek)
            <tr>
                <td>{{ $lek->naziv }}</td>
                <td><code>{{ $lek->jkl_sifra }}</code></td>
                <td>{{ $lek->proizvodjac ?? '-' }}</td>
                <td>{{ $lek->farm_oblik ?? '-' }}</td>
                <td>{{ $lek->jacina ?? '-' }}</td>
                <td>
                    @if($lek->na_recept)
                        <span class="badge bg-warning">Na recept</span>
                    @else
                        <span class="badge bg-success">Bez recepta</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('lekovi.show', $lek) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    @if(Auth::user()->isCentralniAdmin())
                    <a href="{{ route('lekovi.edit', $lek) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">Nema lekova</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $lekovi->links() }}
</div>
@endsection
