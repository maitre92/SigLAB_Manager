@php
    $groupe = $groupe ?? null;
    $formation = $formation ?? $groupe?->formation;
    $formations = $formations ?? collect([$formation])->filter();
    $selectedFormateurs = $selectedFormateurs ?? [];
    $selectedRoles = $selectedRoles ?? [];
    $selectedCommissions = $selectedCommissions ?? [];
    $selectedObservations = $selectedObservations ?? [];
    $nextGroupNumber = $formation ? (($formation->groupes_count ?? $formation->groupes()->count()) + 1) : 1;
    $scheduleSource = old('emploi_du_temps', $groupe->emploi_du_temps ?? '');
    $scheduleRows = json_decode($scheduleSource, true);
    if (!is_array($scheduleRows)) {
        $scheduleRows = $scheduleSource ? [['day' => 'Lundi', 'start' => '', 'end' => '', 'activity' => $scheduleSource]] : [];
    }
    if (empty($scheduleRows)) {
        $scheduleRows = [['day' => 'Lundi', 'start' => '', 'end' => '', 'activity' => '']];
    }
@endphp

@csrf
@if($groupe)
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="card-title mb-0">Informations du groupe</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Formation <span class="text-danger">*</span></label>
                        <select name="formation_id" id="formation_id" class="form-select @error('formation_id') is-invalid @enderror" required>
                            <option value="">Sélectionner une formation...</option>
                            @foreach($formations as $formationOption)
                                <option value="{{ $formationOption->id }}"
                                        data-code="{{ $formationOption->code }}"
                                        data-nom="{{ $formationOption->nom }}"
                                        data-next="{{ ($formationOption->groupes_count ?? $formationOption->groupes()->count()) + 1 }}"
                                        {{ old('formation_id', $formation?->id) == $formationOption->id ? 'selected' : '' }}>
                                    {{ $formationOption->code }} - {{ $formationOption->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('formation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nom du groupe <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $groupe->nom ?? ($formation ? $formation->nom . ' G' . $nextGroupNumber : '')) }}" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $groupe->code ?? ($formation ? $formation->code . '-G' . $nextGroupNumber : '')) }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Formateur principal <span class="text-danger">*</span></label>
                        <select name="formateur_principal_id" class="form-select @error('formateur_principal_id') is-invalid @enderror" required>
                            <option value="">Sélectionner...</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->id }}" {{ old('formateur_principal_id', $groupe->formateur_principal_id ?? null) == $formateur->id ? 'selected' : '' }}>
                                    {{ $formateur->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('formateur_principal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Statut</label>
                        <select name="statut" class="form-select">
                            @foreach(['planifiee' => 'Planifié', 'en_cours' => 'En cours', 'terminee' => 'Terminé', 'suspendue' => 'Suspendu'] as $value => $label)
                                <option value="{{ $value }}" {{ old('statut', $groupe->statut ?? 'planifiee') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Capacité</label>
                        <input type="number" min="0" name="capacite_max" class="form-control" value="{{ old('capacite_max', $groupe->capacite_max ?? $formation?->capacite_max) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date début</label>
                        <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut', optional($groupe?->date_debut)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Date fin</label>
                        <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin', optional($groupe?->date_fin)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Salle</label>
                        <input type="text" name="salle" class="form-control" value="{{ old('salle', $groupe->salle ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Observations</label>
                        <input type="text" name="observations" class="form-control" value="{{ old('observations', $groupe->observations ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

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
                            @foreach($scheduleRows as $index => $item)
                                <tr class="schedule-row">
                                    <td>
                                        <select name="schedule[{{ $index }}][day]" class="form-select form-select-sm">
                                            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $day)
                                                <option value="{{ $day }}" {{ ($item['day'] ?? 'Lundi') === $day ? 'selected' : '' }}>{{ $day }}</option>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="emploi_du_temps" id="emploi_du_temps_json" value="{{ old('emploi_du_temps', $groupe->emploi_du_temps ?? '') }}">
                @error('emploi_du_temps')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Formateurs secondaires</div>
            <div class="card-body">
                @foreach($formateurs as $formateur)
                    <div class="border rounded p-2 mb-2">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="formateurs[]" value="{{ $formateur->id }}" id="formateur-{{ $formateur->id }}" {{ in_array($formateur->id, old('formateurs', $selectedFormateurs)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="formateur-{{ $formateur->id }}">{{ $formateur->name }}</label>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <select name="formateur_roles[{{ $formateur->id }}]" class="form-select form-select-sm">
                                    @foreach(['assistant' => 'Assistant', 'intervenant' => 'Intervenant'] as $value => $label)
                                        <option value="{{ $value }}" {{ old("formateur_roles.{$formateur->id}", $selectedRoles[$formateur->id] ?? 'intervenant') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" min="0" max="100" name="formateur_commissions[{{ $formateur->id }}]" class="form-control form-control-sm" placeholder="%" value="{{ old("formateur_commissions.{$formateur->id}", $selectedCommissions[$formateur->id] ?? '') }}">
                            </div>
                        </div>
                        <input type="text" name="formateur_observations[{{ $formateur->id }}]" class="form-control form-control-sm mt-2" placeholder="Observations" value="{{ old("formateur_observations.{$formateur->id}", $selectedObservations[$formateur->id] ?? '') }}">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn text-white btn-lg" style="background-color: var(--navbar-bg);">
                <i class="fas fa-save me-1"></i> Enregistrer
            </button>
            <a href="{{ route('admin.groupes-formations.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formationSelect = document.getElementById('formation_id');
        const nomInput = document.getElementById('nom');
        const codeInput = document.getElementById('code');
        const form = document.getElementById('groupeFormationForm');
        const scheduleTable = document.querySelector('#scheduleTable tbody');
        const addScheduleRow = document.getElementById('addScheduleRow');
        let rowCount = document.querySelectorAll('.schedule-row').length;

        @if(!$groupe)
            formationSelect?.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];
                if (!option || !option.dataset.code) return;

                const next = option.dataset.next || '1';
                if (!nomInput.value.trim()) {
                    nomInput.value = `${option.dataset.nom} G${next}`;
                }
                if (!codeInput.value.trim()) {
                    codeInput.value = `${option.dataset.code}-G${next}`;
                }
            });
        @endif

        addScheduleRow?.addEventListener('click', function () {
            const row = document.createElement('tr');
            row.classList.add('schedule-row');
            row.innerHTML = `
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
            scheduleTable.appendChild(row);
            rowCount++;
            attachRemoveEvent();
        });

        function attachRemoveEvent() {
            document.querySelectorAll('.remove-row').forEach(btn => {
                btn.onclick = function () {
                    if (scheduleTable.rows.length > 1) this.closest('tr').remove();
                };
            });
        }

        attachRemoveEvent();

        form?.addEventListener('submit', function () {
            const scheduleData = [];
            document.querySelectorAll('.schedule-row').forEach(row => {
                const day = row.querySelector('select').value;
                const inputs = row.querySelectorAll('input');
                const start = inputs[0].value;
                const end = inputs[1].value;
                const activity = inputs[2].value;

                if (start || end || activity) {
                    scheduleData.push({ day, start, end, activity });
                }
            });

            document.getElementById('emploi_du_temps_json').value = JSON.stringify(scheduleData);
        });
    });
</script>
