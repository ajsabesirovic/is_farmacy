@extends('layouts.app')

@section('title', 'Dobavljac - ' . $dobavljac->naziv)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $dobavljac->naziv }}</h1>
    @if($dobavljac->aktivan)
        <span class="badge bg-success fs-6">Aktivan</span>
    @else
        <span class="badge bg-secondary fs-6">Neaktivan</span>
    @endif
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Podaci o dobavljacu</div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="35%">PIB:</th><td><code>{{ $dobavljac->pib }}</code></td></tr>
                    <tr><th>Telefon:</th><td>{{ $dobavljac->telefon ?? '-' }}</td></tr>
                    <tr><th>Email:</th><td>{{ $dobavljac->email ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">Lekovi koje snabdeva ({{ $dobavljac->lekovi->count() }})</div>
            <ul class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                @forelse($dobavljac->lekovi as $lek)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ $lek->naziv }}</span>
                    <span>{{ number_format($lek->pivot->nabavna_cena, 2) }} RSD</span>
                </li>
                @empty
                <li class="list-group-item text-muted">Nema lekova</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<a href="{{ route('dobavljaci.index') }}" class="btn btn-secondary">Nazad</a>
@if(Auth::user()->isCentralniAdmin())
<a href="{{ route('dobavljaci.edit', $dobavljac) }}" class="btn btn-primary">Izmeni</a>
@endif
@endsection
