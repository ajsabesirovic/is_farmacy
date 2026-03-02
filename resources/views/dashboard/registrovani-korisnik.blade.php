@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-search"></i> Pretraga lekova
            </div>
            <div class="card-body">
                <p>Pretrazite dostupnost lekova i uporedite cene u nasim apotekama.</p>
                <a href="{{ route('pretraga') }}" class="btn btn-primary">
                    <i class="bi bi-search"></i> Pretrazi lekove
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
