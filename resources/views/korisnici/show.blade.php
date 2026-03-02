@extends('layouts.app')

@section('title', 'Korisnik - ' . $korisnik->puno_ime)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $korisnik->puno_ime }}</h1>
    @if($korisnik->aktivan)
        <span class="badge bg-success fs-6">Aktivan</span>
    @else
        <span class="badge bg-secondary fs-6">Neaktivan</span>
    @endif
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Podaci o korisniku</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">Ime:</th><td>{{ $korisnik->ime }}</td></tr>
                    <tr><th>Prezime:</th><td>{{ $korisnik->prezime }}</td></tr>
                    <tr><th>Email:</th><td>{{ $korisnik->email }}</td></tr>
                    <tr>
                        <th>Tip:</th>
                        <td>
                            @if($korisnik->isFarmaceut())
                                <span class="badge bg-info">Farmaceut</span>
                            @elseif($korisnik->isAdminApoteke())
                                <span class="badge bg-warning">Admin apoteke</span>
                            @elseif($korisnik->isCentralniAdmin())
                                <span class="badge bg-danger">Centralni admin</span>
                            @elseif($korisnik->isRegistrovaniKorisnik())
                                <span class="badge bg-secondary">Registrovani korisnik</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Apoteka:</th><td>{{ $korisnik->apoteka->naziv ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        @if($korisnik->isFarmaceut() && $korisnik->farmaceut)
        <div class="card mb-4">
            <div class="card-header">Podaci o farmaceutu</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">Broj licence:</th><td>{{ $korisnik->farmaceut->licenca ?? '-' }}</td></tr>
                    <tr><th>Prodaja danas:</th><td>{{ $korisnik->prodaje()->whereDate('datum', today())->count() }}</td></tr>
                    <tr><th>Ukupno prodaja:</th><td>{{ $korisnik->prodaje->count() }}</td></tr>
                </table>
            </div>
        </div>
        @endif
        @if($korisnik->isRegistrovaniKorisnik() && $korisnik->registrovaniKorisnik)
        <div class="card mb-4">
            <div class="card-header">Podaci o registrovanom korisniku</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">JMBG:</th><td>{{ $korisnik->registrovaniKorisnik->jmbg ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<a href="{{ route('korisnici.index') }}" class="btn btn-secondary">Nazad</a>
<a href="{{ route('korisnici.edit', $korisnik) }}" class="btn btn-primary">Izmeni</a>
@endsection
