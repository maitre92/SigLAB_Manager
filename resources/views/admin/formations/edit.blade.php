@extends('layouts.admin')

@section('title', 'Modifier la Formation')
@php $page_title = 'Modifier: ' . $formation->code; @endphp

@section('actions')
    <div class="btn-group">
        <a href="{{ route('admin.formations.show', $formation) }}" class="btn btn-outline-info shadow-sm">
            <i class="fas fa-eye me-1"></i> Voir détails
        </a>
        <a href="{{ route('admin.formations.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <form action="{{ route('admin.formations.update', $formation) }}" method="POST" id="formationForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Informations principales -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i> Informations principales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nom de la formation <span class="text-danger">*</span></label>
                                <input type="text" name="nom" id="formation_nom" class="form-control" value="{{ old('nom', $formation->nom) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Code formation <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="code" id="formation_code" class="form-control" value="{{ old('code', $formation->code) }}" required>
                                    <button class="btn btn-outline-secondary" type="button" id="generateCodeBtn">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Catégorie <span class="text-danger">*</span></label>
                                <select name="categorie_formation_id" class="form-select" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('categorie_formation_id', $formation->categorie_formation_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="4">{{ old('description', $formation->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations pédagogiques -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                        <h5 class="card-title mb-0"><i class="fas fa-graduation-cap me-2"></i> Informations pédagogiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Type de formation <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="Présentiel" {{ old('type', $formation->type) == 'Présentiel' ? 'selected' : '' }}>Présentiel</option>
                                    <option value="En ligne" {{ old('type', $formation->type) == 'En ligne' ? 'selected' : '' }}>En ligne</option>
                                    <option value="Hybride" {{ old('type', $formation->type) == 'Hybride' ? 'selected' : '' }}>Hybride</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Niveau</label>
                                <input type="text" name="niveau" class="form-control" value="{{ old('niveau', $formation->niveau) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Statut <span class="text-danger">*</span></label>
                                <select name="statut" class="form-select" required>
                                    <option value="planifiee" {{ old('statut', $formation->statut) == 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                                    <option value="en_cours" {{ old('statut', $formation->statut) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="terminee" {{ old('statut', $formation->statut) == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                    <option value="suspendue" {{ old('statut', $formation->statut) == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Durée (Heures)</label>
                                <div class="input-group">
                                    <input type="number" name="duree_heures" class="form-control" value="{{ old('duree_heures', $formation->duree_heures) }}" min="0">
                                    <span class="input-group-text">h</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coût (FCFA)</label>
                                <div class="input-group">
                                    <input type="number" name="cout" class="form-control" value="{{ old('cout', $formation->cout) }}" min="0">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Capacité Max</label>
                                <div class="input-group">
                                    <input type="number" name="capacite_max" class="form-control" value="{{ old('capacite_max', $formation->capacite_max) }}" min="0">
                                    <span class="input-group-text">élèves</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emploi du temps -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: var(--navbar-bg);">
                        <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i> Emploi du temps</h5>
                        <button type="button" class="btn btn-sm btn-light" id="addScheduleRow">
                            <i class="fas fa-plus me-1"></i> Add row
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="scheduleTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jour</th>
                                        <th>Heure Début</th>
                                        <th>Heure Fin</th>
                                        <th>Activité / Module</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $schedule = json_decode($formation->emploi_du_temps, true) ?: [];
                                    @endphp
                                    @forelse($schedule as $index => $item)
                                    <tr class="schedule-row">
                                        <td>
                                            <select name="schedule[{{ $index }}][day]" class="form-select form-select-sm">
                                                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $day)
                                                    <option value="{{ $day }}" {{ ($item['day'] ?? '') == $day ? 'selected' : '' }}>{{ $day }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="time" name="schedule[{{ $index }}][start]" class="form-control form-control-sm" value="{{ $item['start'] ?? '' }}"></td>
                                        <td><input type="time" name="schedule[{{ $index }}][end]" class="form-control form-control-sm" value="{{ $item['end'] ?? '' }}"></td>
                                        <td><input type="text" name="schedule[{{ $index }}][activity]" class="form-control form-control-sm" value="{{ $item['activity'] ?? '' }}"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="schedule-row">
                                        <td>
                                            <select name="schedule[0][day]" class="form-select form-select-sm">
                                                <option value="Lundi">Lundi</option>
                                                <option value="Mardi">Mardi</option>
                                                <option value="Mercredi">Mercredi</option>
                                                <option value="Jeudi">Jeudi</option>
                                                <option value="Vendredi">Vendredi</option>
                                                <option value="Samedi">Samedi</option>
                                                <option value="Dimanche">Dimanche</option>
                                            </select>
                                        </td>
                                        <td><input type="time" name="schedule[0][start]" class="form-control form-control-sm"></td>
                                        <td><input type="time" name="schedule[0][end]" class="form-control form-control-sm"></td>
                                        <td><input type="text" name="schedule[0][activity]" class="form-control form-control-sm"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <input type="hidden" name="emploi_du_temps" id="emploi_du_temps_json">
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Organisation -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                        <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i> Organisation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Salle / Lieu</label>
                                <input type="text" name="salle" class="form-control" value="{{ old('salle', $formation->salle) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date début</label>
                                <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut', $formation->date_debut ? $formation->date_debut->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Date fin</label>
                                <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin', $formation->date_fin ? $formation->date_fin->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0">Formateurs affectés</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showAllRoles">
                                        <label class="form-check-label small" for="showAllRoles">Tous les rôles</label>
                                    </div>
                                </div>
                                <select name="formateurs[]" id="formateurs_select" class="form-control" multiple>
                                    @foreach($formateurs as $formateur)
                                        <option value="{{ $formateur->id }}" 
                                                data-role="{{ $formateur->role }}"
                                                {{ in_array($formateur->id, old('formateurs', $selectedFormateurs)) ? 'selected' : '' }}>
                                            {{ $formateur->name }} ({{ $formateur->role }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn text-white py-3 shadow" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-2"></i> Mettre à jour la formation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Choices.js with filtering
    const selectElement = document.getElementById('formateurs_select');
    const allOptions = Array.from(selectElement.options).map(opt => ({
        value: opt.value,
        label: opt.text,
        role: opt.dataset.role,
        selected: opt.selected
    }));

    let choicesInstance = new Choices(selectElement, {
        removeItemButton: true,
        placeholderValue: 'Sélectionner des formateurs',
    });

    const showAllCheckbox = document.getElementById('showAllRoles');
    const roleFormateur = "{{ \App\Shared\Enums\UserRole::FORMATEUR->value }}";

    function updateTrainerList() {
        const showAll = showAllCheckbox.checked;
        const filteredChoices = allOptions.filter(opt => {
            if (opt.selected) return true;
            return showAll || opt.role === roleFormateur;
        });

        choicesInstance.destroy();
        selectElement.innerHTML = '';
        filteredChoices.forEach(opt => {
            const newOpt = new Option(opt.label, opt.value, opt.selected, opt.selected);
            newOpt.dataset.role = opt.role;
            selectElement.add(newOpt);
        });

        choicesInstance = new Choices(selectElement, {
            removeItemButton: true,
            placeholderValue: 'Sélectionner des formateurs',
        });
    }

    showAllCheckbox.addEventListener('change', updateTrainerList);
    updateTrainerList();

    selectElement.addEventListener('change', function() {
        const selectedValues = Array.from(selectElement.selectedOptions).map(opt => opt.value);
        allOptions.forEach(opt => opt.selected = selectedValues.includes(opt.value));
    });

    // 2. Code Generation
    const nomInput = document.getElementById('formation_nom');
    const codeInput = document.getElementById('formation_code');
    const genBtn = document.getElementById('generateCodeBtn');

    genBtn.addEventListener('click', function() {
        if (nomInput.value) {
            let slug = nomInput.value.toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/[^A-Z0-9]/g, '-').replace(/-+/g, '-').substring(0, 10);
            let random = Math.floor(100 + Math.random() * 900);
            codeInput.value = slug + '-' + random;
        }
    });

    // 3. Dynamic Schedule
    const scheduleTable = document.getElementById('scheduleTable').getElementsByTagName('tbody')[0];
    const addRowBtn = document.getElementById('addScheduleRow');
    let rowCount = {{ count($schedule) > 0 ? count($schedule) : 1 }};

    addRowBtn.addEventListener('click', function() {
        const newRow = scheduleTable.insertRow();
        newRow.className = 'schedule-row';
        newRow.innerHTML = `
            <td>
                <select name="schedule[${rowCount}][day]" class="form-select form-select-sm">
                    <option value="Lundi">Lundi</option>
                    <option value="Mardi">Mardi</option>
                    <option value="Mercredi">Mercredi</option>
                    <option value="Jeudi">Jeudi</option>
                    <option value="Vendredi">Vendredi</option>
                    <option value="Samedi">Samedi</option>
                    <option value="Dimanche">Dimanche</option>
                </select>
            </td>
            <td><input type="time" name="schedule[${rowCount}][start]" class="form-control form-control-sm"></td>
            <td><input type="time" name="schedule[${rowCount}][end]" class="form-control form-control-sm"></td>
            <td><input type="text" name="schedule[${rowCount}][activity]" class="form-control form-control-sm"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button>
            </td>
        `;
        rowCount++;
        attachRemoveEvent();
    });

    function attachRemoveEvent() {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = function() { if (scheduleTable.rows.length > 1) this.closest('tr').remove(); };
        });
    }
    attachRemoveEvent();

    const form = document.getElementById('formationForm');
    form.onsubmit = function() {
        const rows = document.querySelectorAll('.schedule-row');
        const scheduleData = [];
        rows.forEach(row => {
            const day = row.querySelector('select').value;
            const start = row.querySelectorAll('input')[0].value;
            const end = row.querySelectorAll('input')[1].value;
            const activity = row.querySelectorAll('input')[2].value;
            if (start || end || activity) scheduleData.push({ day, start, end, activity });
        });
        document.getElementById('emploi_du_temps_json').value = JSON.stringify(scheduleData);
    };
});
</script>

<style>
    .choices__inner { border-radius: 8px !important; }
    .choices__list--multiple .choices__item { background-color: var(--navbar-bg) !important; border: 1px solid var(--navbar-bg) !important; }
</style>
@endsection
