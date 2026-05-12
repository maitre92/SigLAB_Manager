@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Paramètres de l'application</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="app_name" class="form-label">Nom de l'application *</label>
                        <input type="text" class="form-control @error('app_name') is-invalid @enderror" 
                               id="app_name" name="app_name" 
                               value="{{ old('app_name', $settings['app_name'] ?? config('app.name')) }}" required>
                        @error('app_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="app_description" class="form-label">Description</label>
                        <textarea class="form-control @error('app_description') is-invalid @enderror" 
                                  id="app_description" name="app_description" rows="3">{{ old('app_description', '') }}</textarea>
                        @error('app_description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="items_per_page" class="form-label">Éléments par page *</label>
                        <input type="number" class="form-control @error('items_per_page') is-invalid @enderror" 
                               id="items_per_page" name="items_per_page" min="5" max="100"
                               value="{{ old('items_per_page', 15) }}" required>
                        @error('items_per_page')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-3">
                        <h6>Paramètres de sécurité</h6>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="require_2fa" name="require_2fa">
                        <label class="form-check-label" for="require_2fa">
                            Forcer l'authentification à deux facteurs
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="enable_audit_log" name="enable_audit_log" checked>
                        <label class="form-check-label" for="enable_audit_log">
                            Activer les logs d'audit
                        </label>
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les paramètres
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            Réinitialiser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-header bg-white">
                <h5 class="mb-0">Information système</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>sigLAB:</strong> {{ app()->version() }}
                    </li>
                    <li class="mb-2">
                        <strong>PHP:</strong> {{ PHP_VERSION }}
                    </li>
                    <li class="mb-2">
                        <strong>Environnement:</strong> 
                        <span class="badge bg-info">{{ config('app.env') }}</span>
                    </li>
                    <li class="mb-2">
                        <strong>Debug:</strong> 
                        <span class="badge {{ config('app.debug') ? 'bg-warning' : 'bg-success' }}">
                            {{ config('app.debug') ? 'Activé' : 'Désactivé' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">Maintenance</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Outils de maintenance de l'application.</p>
                <a href="#" class="btn btn-sm btn-outline-warning w-100 mb-2">
                    <i class="fas fa-broom"></i> Vider le cache
                </a>
                <a href="#" class="btn btn-sm btn-outline-danger w-100">
                    <i class="fas fa-sync"></i> Réinitialiser
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
