@extends('layouts.app')

@section('title', 'Apoteka - ' . $apoteka->naziv)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $apoteka->naziv }}</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Podaci o apoteci</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">Grad:</th><td>{{ $apoteka->grad }}</td></tr>
                    <tr><th>Adresa:</th><td>{{ $apoteka->adresa }}</td></tr>
                    <tr><th>Telefon:</th><td>{{ $apoteka->telefon ?? '-' }}</td></tr>
                    <tr><th>Email:</th><td>{{ $apoteka->email ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Statistika</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="50%">Broj zaposlenih:</th><td>{{ $apoteka->korisnici->count() }}</td></tr>
                    <tr><th>Lekova na zalihama:</th><td>{{ $apoteka->zalihe->count() }}</td></tr>
                    <tr><th>Broj prodaja:</th><td>{{ $apoteka->prodaje->count() }}</td></tr>
                    <tr><th>Narudzbenica:</th><td>{{ $apoteka->narudzbenice->count() }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Zaposleni ({{ $apoteka->korisnici->count() }})</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Ime i prezime</th><th>Email</th><th>Tip</th></tr></thead>
            <tbody>
                @forelse($apoteka->korisnici as $korisnik)
                <tr>
                    <td>{{ $korisnik->puno_ime }}</td>
                    <td>{{ $korisnik->email }}</td>
                    <td>
                        @if($korisnik->isFarmaceut())
                            <span class="badge bg-info">Farmaceut</span>
                        @elseif($korisnik->isAdminApoteke())
                            <span class="badge bg-warning">Admin apoteke</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-muted">Nema zaposlenih</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<a href="{{ route('apoteke.index') }}" class="btn btn-secondary">Nazad</a>
@if(Auth::user()->isCentralniAdmin())
<a href="{{ route('apoteke.edit', $apoteka) }}" class="btn btn-primary">Izmeni</a>
@endif
@endsection
