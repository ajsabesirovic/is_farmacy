@extends('layouts.app')

@section('title', 'Dobavljaci')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dobavljaci</h1>
    @if(Auth::user()->isCentralniAdmin())
    <a href="{{ route('dobavljaci.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Dodaj dobavljaca</a>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('dobavljaci.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" name="search" placeholder="Naziv ili PIB..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="samo_aktivni" value="1" id="samoAktivni" {{ request('samo_aktivni') ? 'checked' : '' }}>
                    <label class="form-check-label" for="samoAktivni">Samo aktivni</label>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">Pretrazi</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr><th>Naziv</th><th>PIB</th><th>Telefon</th><th>Email</th><th>Lekova</th><th>Status</th><th>Akcije</th></tr>
        </thead>
        <tbody>
            @forelse($dobavljaci as $dobavljac)
            <tr>
                <td>{{ $dobavljac->naziv }}</td>
                <td><code>{{ $dobavljac->pib }}</code></td>
                <td>{{ $dobavljac->telefon ?? '-' }}</td>
                <td>{{ $dobavljac->email ?? '-' }}</td>
                <td>{{ $dobavljac->lekovi_count }}</td>
                <td>
                    @if($dobavljac->aktivan)
                        <span class="badge bg-success">Aktivan</span>
                    @else
                        <span class="badge bg-secondary">Neaktivan</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('dobavljaci.show', $dobavljac) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                    @if(Auth::user()->isCentralniAdmin())
                    <a href="{{ route('dobavljaci.edit', $dobavljac) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Nema dobavljaca</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="d-flex justify-content-center mt-4">
    {{ $dobavljaci->links() }}
</div>
@endsection
