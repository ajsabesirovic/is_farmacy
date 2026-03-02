@extends('layouts.app')

@section('title', 'Korisnici')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Korisnici</h1>
    <a href="{{ route('korisnici.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Dodaj korisnika</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('korisnici.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Ime, prezime ili email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="tip">
                    <option value="">Svi tipovi</option>
                    <option value="F" {{ request('tip') == 'F' ? 'selected' : '' }}>Farmaceuti</option>
                    <option value="A" {{ request('tip') == 'A' ? 'selected' : '' }}>Admini apoteka</option>
                    <option value="C" {{ request('tip') == 'C' ? 'selected' : '' }}>Centralni admini</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="apoteka_id">
                    <option value="">Sve apoteke</option>
                    @foreach($apoteke ?? [] as $apoteka)
                    <option value="{{ $apoteka->id }}" {{ request('apoteka_id') == $apoteka->id ? 'selected' : '' }}>{{ $apoteka->naziv }}</option>
                    @endforeach
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
            <tr><th>Ime i prezime</th><th>Email</th><th>Tip</th><th>Apoteka</th><th>Akcije</th></tr>
        </thead>
        <tbody>
            @forelse($korisnici as $korisnik)
            <tr>
                <td>{{ $korisnik->puno_ime }}</td>
                <td>{{ $korisnik->email }}</td>
                <td>
                    @if($korisnik->isFarmaceut())
                        <span class="badge bg-info">Farmaceut</span>
                    @elseif($korisnik->isAdminApoteke())
                        <span class="badge bg-warning">Admin apoteke</span>
                    @elseif($korisnik->isCentralniAdmin())
                        <span class="badge bg-danger">Centralni admin</span>
                    @endif
                </td>
                <td>{{ $korisnik->apoteka->naziv ?? '-' }}</td>
                <td>
                    <a href="{{ route('korisnici.show', $korisnik) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('korisnici.edit', $korisnik) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted">Nema korisnika</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if(method_exists($korisnici, 'links'))
<div class="d-flex justify-content-center mt-4">
    {{ $korisnici->links() }}
</div>
@endif
@endsection
