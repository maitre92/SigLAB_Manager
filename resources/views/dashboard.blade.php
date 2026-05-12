@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>
        <i class="fas fa-home"></i> Dashboard
    </h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Dashboard Vide -->
<div class="text-center py-5">
    <i class="fas fa-inbox" style="font-size: 64px; color: #ccc;"></i>
    <h3 class="mt-4 text-muted">Dashboard</h3>
    <p class="text-muted">Bienvenue dans le tableau de bord de sigLAB Manager</p>
</div>
@endsection
