@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.pedagogie.presences') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Formation</label>
                        <select name="formation_id" id="formation_select" class="form-select">
                            <option value="">Sélectionnez une formation</option>
                            @foreach($formations as $f)
                                <option value="{{ $f->id }}" {{ $formationId == $f->id ? 'selected' : '' }}>
                                    {{ $f->nom }} ({{ $f->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3" id="groupe_container" style="display: none;">
                        <label class="form-label fw-bold">Groupe</label>
                        <select name="groupe_id" id="groupe_select" class="form-select" onchange="this.form.submit()">
                            <option value="">Tous les apprenants (Sans groupe)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">
                            <i class="fas fa-sync-alt me-1"></i> Actualiser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($formationId)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-check text-primary me-2"></i> Appel du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h6>
        </div>
        <form action="{{ route('admin.pedagogie.presences.store') }}" method="POST">
            @csrf
            <input type="hidden" name="formation_id" value="{{ $formationId }}">
            <input type="hidden" name="groupe_id" value="{{ $groupeId }}">
            <input type="hidden" name="date" value="{{ $date }}">
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Apprenant</th>
                            <th class="text-center">Présent</th>
                            <th class="text-center">Absent</th>
                            <th class="text-center">Retard</th>
                            <th class="text-center">Justifié</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apprenants as $apprenant)
                            @php
                                $status = $presences[$apprenant->id]->statut ?? 'present';
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $apprenant->nom_complet }}</div>
                                    <div class="text-muted small">{{ $apprenant->matricule }}</div>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="radio" name="presences[{{ $apprenant->id }}]" value="present" {{ $status == 'present' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="radio" name="presences[{{ $apprenant->id }}]" value="absent" {{ $status == 'absent' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="radio" name="presences[{{ $apprenant->id }}]" value="retard" {{ $status == 'retard' ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input" type="radio" name="presences[{{ $apprenant->id }}]" value="justifie" {{ $status == 'justifie' ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">
                                    <i class="fas fa-users fa-2x mb-3 text-muted opacity-50 d-block"></i>
                                    Aucun apprenant trouvé pour cette sélection.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($apprenants) > 0)
            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn text-white shadow-sm" style="background-color: var(--navbar-bg);">
                    <i class="fas fa-save me-1"></i> Enregistrer les présences
                </button>
            </div>
            @endif
        </form>
    </div>
@else
    <div class="card border-0 shadow-sm py-5">
        <div class="card-body text-center text-muted">
            <i class="fas fa-chalkboard-teacher mb-3" style="font-size: 48px; opacity: 0.2;"></i>
            <p class="mb-0">Veuillez sélectionner une formation pour faire l'appel.</p>
        </div>
    </div>
@endif
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formationsData = @json($formations);
        const formationSelect = document.getElementById('formation_select');
        const groupeContainer = document.getElementById('groupe_container');
        const groupeSelect = document.getElementById('groupe_select');
        const selectedGroupeId = "{{ $groupeId }}";

        function updateGroupes() {
            const formationId = formationSelect.value;
            groupeSelect.innerHTML = '<option value="">Tous les apprenants (Sans groupe)</option>';
            
            if (!formationId) {
                groupeContainer.style.display = 'none';
                return;
            }

            const formation = formationsData.find(f => f.id == formationId);
            if (formation && formation.groupes && formation.groupes.length > 0) {
                formation.groupes.forEach(g => {
                    const option = document.createElement('option');
                    option.value = g.id;
                    option.textContent = g.nom;
                    if (g.id == selectedGroupeId) {
                        option.selected = true;
                    }
                    groupeSelect.appendChild(option);
                });
                groupeContainer.style.display = 'block';
            } else {
                groupeContainer.style.display = 'none';
            }
        }

        formationSelect.addEventListener('change', function() {
            updateGroupes();
            this.form.submit();
        });

        // Initial call
        if (formationSelect.value) {
            updateGroupes();
        }
    });
</script>
@endsection

