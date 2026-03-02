@extends('layouts.app')

@section('title', 'Apoteke')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Apoteke</h1>
    @if(Auth::user()->isCentralniAdmin())
    <a href="{{ route('apoteke.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Dodaj apoteku</a>
    @endif
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr><th>Naziv</th><th>Grad</th><th>Adresa</th><th>Telefon</th><th>Zaposlenih</th><th>Akcije</th></tr>
        </thead>
        <tbody>
            @forelse($apoteke as $apoteka)
            <tr>
                <td>{{ $apoteka->naziv }}</td>
                <td>{{ $apoteka->grad }}</td>
                <td>{{ $apoteka->adresa }}</td>
                <td>{{ $apoteka->telefon ?? '-' }}</td>
                <td>{{ $apoteka->korisnici_count ?? $apoteka->korisnici->count() }}</td>
                <td>
                    <a href="{{ route('apoteke.show', $apoteka) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                    @if(Auth::user()->isCentralniAdmin())
                    <a href="{{ route('apoteke.edit', $apoteka) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted">Nema apoteka</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@if(method_exists($apoteke, 'links'))
<div class="d-flex justify-content-center mt-4">
    {{ $apoteke->links() }}
</div>
@endif
@endsection
