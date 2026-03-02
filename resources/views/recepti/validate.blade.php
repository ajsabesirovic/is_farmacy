@extends('layouts.app')

@section('title', 'Validacija recepta')

@section('content')
<div class="pb-2 mb-3 border-bottom">
    <h1 class="h2">Validacija recepta</h1>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('recepti.validacija') }}" method="GET">
                    <div class="mb-3">
                        <label class="form-label">Unesite broj recepta</label>
                        <input type="text" class="form-control form-control-lg" name="broj_recepta"
                               value="{{ request('broj_recepta') }}" placeholder="Broj recepta..." autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-search"></i> Proveri
                    </button>
                </form>
            </div>
        </div>

        @if(isset($recept))
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Rezultat pretrage</span>
                <span class="badge bg-{{ $recept->status->color() }}">{{ $recept->status->label() }}</span>
            </div>
            <div class="card-body">
                <h5>{{ $recept->broj_recepta }}</h5>
                <p class="mb-1"><strong>Pacijent:</strong> {{ $recept->ime_pacijenta ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Vazi do:</strong> {{ $recept->datum_vazenja->format('d.m.Y') }}</p>
                <hr>
                <h6>Lekovi:</h6>
                <ul>
                    @foreach($recept->lekovi as $lek)
                    <li>{{ $lek->naziv }} - {{ $lek->pivot->kolicina }} kom</li>
                    @endforeach
                </ul>

                @if($recept->isValid())
                <a href="{{ route('prodaje.create') }}?recept={{ $recept->id }}" class="btn btn-success w-100">
                    <i class="bi bi-cart"></i> Realizuj
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
