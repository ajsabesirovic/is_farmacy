@extends('layouts.app')

@section('title', 'Zalihe')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Zalihe</h1>
</div>

@if($niskeZalihe->isNotEmpty())
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>{{ $niskeZalihe->count() }}</strong> artikala ima niske zalihe!
</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('zalihe.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Pretrazi po nazivu leka ili JKL sifri..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="niske_zalihe" value="1" id="niskeZalihe"
                           {{ request('niske_zalihe') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="niskeZalihe">Samo niske zalihe</label>
                </div>
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
                @if(Auth::user()->isCentralniAdmin())
                <th>Apoteka</th>
                @endif
                <th>Lek</th>
                <th>JKL Sifra</th>
                <th>Kolicina</th>
                <th>Min. zaliha</th>
                <th>Cena</th>
                <th>Status</th>
                @if(!Auth::user()->isFarmaceut())
                <th>Akcije</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($zalihe as $zaliha)
            <tr class="{{ $zaliha->isLowStock() ? 'table-warning' : '' }} {{ $zaliha->isOutOfStock() ? 'table-danger' : '' }}">
                @if(Auth::user()->isCentralniAdmin())
                <td>{{ $zaliha->apoteka->naziv }}</td>
                @endif
                <td>{{ $zaliha->lek->naziv }}</td>
                <td><code>{{ $zaliha->lek->jkl_sifra }}</code></td>
                <td class="{{ $zaliha->isLowStock() ? 'text-danger fw-bold' : '' }}">
                    {{ $zaliha->kolicina }}
                </td>
                <td>{{ $zaliha->min_zaliha }}</td>
                <td>{{ number_format($zaliha->prodajna_cena, 2) }} RSD</td>
                <td>
                    @if($zaliha->isOutOfStock())
                        <span class="badge bg-danger">Nema</span>
                    @elseif($zaliha->isLowStock())
                        <span class="badge bg-warning">Nisko</span>
                    @else
                        <span class="badge bg-success">OK</span>
                    @endif
                </td>
                @if(!Auth::user()->isFarmaceut())
                <td>
                    <a href="{{ route('zalihe.edit', [$zaliha->apoteka_id, $zaliha->lek_id]) }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ Auth::user()->isCentralniAdmin() ? 8 : (Auth::user()->isFarmaceut() ? 6 : 7) }}" class="text-center text-muted">
                    Nema zaliha
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $zalihe->links() }}
</div>
@endsection
