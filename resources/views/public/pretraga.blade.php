@extends('layouts.app')

@section('title', 'Pretraga lekova')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Pronadi lek u nasim apotekama</h1>
</div>

<div class="card mb-4">
    <div class="card-body">
        <p class="text-muted mb-3">Pretrazite dostupnost lekova, uporedite cene i pronadite najblizu apoteku.</p>
        <form action="{{ route('pretraga') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <div class="input-group input-group-lg">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="q" placeholder="Unesite naziv leka ili JKL sifru..."
                           value="{{ $query ?? '' }}" required>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-lg" name="grad">
                    <option value="">Svi gradovi</option>
                    @foreach($gradovi as $grad)
                        <option value="{{ $grad }}" {{ ($izabraniGrad ?? '') == $grad ? 'selected' : '' }}>
                            {{ $grad }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="bi bi-search"></i> Pretrazi
                </button>
            </div>
        </form>
    </div>
</div>

@if(isset($rezultati))
    @if($rezultati->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Nema rezultata za vasu pretragu "{{ $query }}".
        </div>
    @else
        <h4 class="mb-4">Pronadjeno {{ $rezultati->count() }} lekova</h4>

        @foreach($rezultati as $lek)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">{{ $lek['naziv'] }}</h5>
                    <small class="text-muted">
                        {{ $lek['proizvodjac'] }} | JKL: {{ $lek['jkl_sifra'] }}
                        @if($lek['jacina']) | {{ $lek['jacina'] }} @endif
                        @if($lek['farm_oblik']) | {{ $lek['farm_oblik'] }} @endif
                    </small>
                </div>
                <div class="text-end">
                    @if($lek['na_recept'])
                        <span class="badge bg-warning">Na recept</span>
                    @else
                        <span class="badge bg-success">Bez recepta</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-center">
                        <h6 class="text-muted">Dostupno u</h6>
                        <h3>{{ $lek['dostupnost']->count() }} apoteka</h3>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="text-muted">Ukupno na zalihama</h6>
                        <h3>{{ $lek['ukupno_dostupno'] }} kom</h3>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="text-muted">Cena od</h6>
                        <h3>{{ number_format($lek['min_cena'], 2) }} RSD</h3>
                    </div>
                </div>

                <h6>Dostupnost po apotekama:</h6>
                <div class="row">
                    @foreach($lek['dostupnost'] as $dostupnost)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100" style="border-left: 4px solid #0d6efd;">
                            <div class="card-body">
                                <h6 class="card-title">{{ $dostupnost['apoteka_naziv'] }}</h6>
                                <p class="card-text mb-1">
                                    <i class="bi bi-geo-alt"></i> {{ $dostupnost['adresa'] }}, {{ $dostupnost['grad'] }}
                                </p>
                                @if($dostupnost['telefon'])
                                <p class="card-text mb-1">
                                    <i class="bi bi-telephone"></i> {{ $dostupnost['telefon'] }}
                                </p>
                                @endif
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span><strong>Kolicina:</strong> {{ $dostupnost['kolicina'] }} kom</span>
                                    <span><strong>Cena:</strong> {{ number_format($dostupnost['prodajna_cena'], 2) }} RSD</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    @endif
@else
    <div class="text-center py-5">
        <i class="bi bi-capsule text-primary" style="font-size: 5rem;"></i>
        <h3 class="mt-4">Unesite naziv leka za pretragu</h3>
        <p class="text-muted">Pronacicemo dostupnost u svim nasim apotekama</p>
    </div>
@endif
@endsection
