@extends('layouts.admin')

@section('title', 'Ajouter une Formation')
@php $page_title = 'Nouvelle Formation'; @endphp

@section('actions')
    <a href="{{ route('admin.formations.index') }}" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
    </a>
@endsection

@section('content')
<div class="container-fluid p-0">
    <form action="{{ route('admin.formations.store') }}" method="POST" id="formationForm">
        @csrf
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Informations principales</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fw-bold">Nom de la formation <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="formation_nom" class="form-control" value="{{ old('nom') }}" required placeholder="ex: Formation Complète en Développement Web">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Catégorie <span class="text-danger">*</span></label>
                        <select name="categorie_formation_id" class="form-select" required>
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('categorie_formation_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Objectifs, prérequis, contenu... ">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-graduation-cap me-2"></i> Informations pédagogiques</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Type de formation <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="Présentiel" {{ old('type') == 'Présentiel' ? 'selected' : '' }}>Présentiel</option>
                            <option value="En ligne" {{ old('type') == 'En ligne' ? 'selected' : '' }}>En ligne</option>
                            <option value="Hybride" {{ old('type') == 'Hybride' ? 'selected' : '' }}>Hybride</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Niveau</label>
                        <select name="niveau" class="form-select">
                            <option value="">Sélectionner un niveau...</option>
                            @foreach(['Débutant', 'Intermédiaire', 'Avancé'] as $niveau)
                                <option value="{{ $niveau }}" {{ old('niveau') === $niveau ? 'selected' : '' }}>{{ $niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Durée</label>
                        <div class="input-group">
                            <input type="number" name="duree" class="form-control @error('duree') is-invalid @enderror" value="{{ old('duree', 0) }}" min="0">
                            <select name="duree_unite" id="duree_unite_select" class="form-select" style="max-width: 110px;">
                                <option value="heures" {{ old('duree_unite', 'heures') === 'heures' ? 'selected' : '' }}>Heures</option>
                                <option value="mois" {{ old('duree_unite', 'heures') === 'mois' ? 'selected' : '' }}>Mois</option>
                            </select>
                        </div>
                        @error('duree')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 {{ old('duree_unite', 'heures') === 'mois' ? '' : 'd-none' }}" id="volume_horaire_wrapper">
                        <label class="form-label fw-bold">Volume horaire <small class="text-muted fw-normal">(facultatif)</small></label>
                        <div class="input-group">
                            <input type="number" name="volume_horaire" class="form-control @error('volume_horaire') is-invalid @enderror" value="{{ old('volume_horaire') }}" min="0">
                            <span class="input-group-text">h</span>
                        </div>
                        <div class="form-text">Utilisé pour le suivi des heures et la paie des formateurs.</div>
                        @error('volume_horaire')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <script>
                        document.getElementById('duree_unite_select').addEventListener('change', function () {
                            document.getElementById('volume_horaire_wrapper').classList.toggle('d-none', this.value !== 'mois');
                        });
                    </script>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Coût (FCFA)</label>
                        <div class="input-group">
                            <input type="number" name="cout" class="form-control" value="{{ old('cout', 0) }}" min="0">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Frais d'inscription (FCFA)</label>
                        <div class="input-group">
                            <input type="number" name="frais_inscription" class="form-control" value="{{ old('frais_inscription', 0) }}" min="0">
                            <span class="input-group-text">FCFA</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Capacité Max</label>
                        <div class="input-group">
                            <input type="number" name="capacite_max" class="form-control" value="{{ old('capacite_max', 0) }}" min="0">
                            <span class="input-group-text">élèves</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i> Organisation</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn text-white px-4 py-3 shadow" style="background-color: var(--navbar-bg);">
                <i class="fas fa-save me-2"></i> Enregistrer la formation
            </button>
        </div>
    </form>
</div>
@endsection
