@extends('layouts.app')

@section('title', 'Dashboard - Farmaceut')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <a href="{{ route('prodaje.create') }}" class="btn btn-primary">
        <i class="bi bi-cart-plus"></i> Nova prodaja
    </a>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card card-stat primary">
            <div class="card-body">
                <h5 class="card-title text-muted">Prodaje danas</h5>
                <h2 class="mb-0">{{ $danas_prodaja }}</h2>
            </div>
        </div>
    </div>
</div>

@if($niske_zalihe->isNotEmpty())
<div class="card mb-4">
    <div class="card-header bg-warning text-dark">
        <i class="bi bi-exclamation-triangle"></i> Upozorenje - Niske zalihe
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Lek</th>
                        <th>Trenutno</th>
                        <th>Minimum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($niske_zalihe as $zaliha)
                    <tr>
                        <td>{{ $zaliha->lek->naziv }}</td>
                        <td class="text-danger fw-bold">{{ $zaliha->kolicina }}</td>
                        <td>{{ $zaliha->min_zaliha }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning"></i> Brze akcije
            </div>
            <div class="card-body">
                <a href="{{ route('prodaje.create') }}" class="btn btn-outline-primary mb-2 w-100">
                    <i class="bi bi-cart-plus"></i> Nova prodaja
                </a>
                <a href="{{ route('recepti.validacija') }}" class="btn btn-outline-info mb-2 w-100">
                    <i class="bi bi-qr-code-scan"></i> Validiraj recept
                </a>
                <a href="{{ route('zalihe.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-box-seam"></i> Pregled zaliha
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
