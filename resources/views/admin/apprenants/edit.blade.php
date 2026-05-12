@extends('layouts.admin')

@section('title', 'Modifier: ' . $apprenant->nom_complet)

@section('actions')
    <a href="{{ route('admin.apprenants.show', $apprenant) }}" class="btn btn-outline-secondary me-2">
        <i class="fas fa-times"></i> Annuler
    </a>
@endsection

@section('content')
<form action="{{ route('admin.apprenants.update', $apprenant) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- Informations personnelles -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white py-3 d-flex justify-content-between align-items-center" style="background-color: var(--navbar-bg);">
                    <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i> Informations personnelles</h5>
                    <span class="badge bg-light text-dark border px-3 py-2">Matricule: {{ $apprenant->matricule }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" value="{{ old('prenom', $apprenant->prenom) }}" required>
                            @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $apprenant->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $apprenant->date_naissance?->format('Y-m-d')) }}">
                            @error('date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label d-block">Sexe <span class="text-danger">*</span></label>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input @error('sexe') is-invalid @enderror" type="radio" name="sexe" id="sexeM" value="M" {{ old('sexe', $apprenant->sexe) == 'M' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="sexeM">Masculin</label>
                            </div>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input @error('sexe') is-invalid @enderror" type="radio" name="sexe" id="sexeF" value="F" {{ old('sexe', $apprenant->sexe) == 'F' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="sexeF">Féminin</label>
                            </div>
                            @error('sexe')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $apprenant->telephone) }}">
                            @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $apprenant->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="2">{{ old('adresse', $apprenant->adresse) }}</textarea>
                            @error('adresse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations académiques -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                    <h5 class="card-title mb-0"><i class="fas fa-graduation-cap me-2"></i> Informations académiques</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="niveau_etude" class="form-label">Niveau d'étude <span class="text-danger">*</span></label>
                            <select class="form-select @error('niveau_etude') is-invalid @enderror" id="niveau_etude" name="niveau_etude" required>
                                <option value="">-- Sélectionner un niveau --</option>
                                @foreach($niveaux as $value => $label)
                                    <option value="{{ $value }}" {{ old('niveau_etude', $apprenant->niveau_etude->value ?? $apprenant->niveau_etude) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('niveau_etude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="profession" class="form-label">Profession</label>
                            <input type="text" class="form-control @error('profession') is-invalid @enderror" id="profession" name="profession" value="{{ old('profession', $apprenant->profession) }}">
                            @error('profession')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_inscription" class="form-label">Date d'inscription <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_inscription') is-invalid @enderror" id="date_inscription" name="date_inscription" value="{{ old('date_inscription', $apprenant->date_inscription?->format('Y-m-d')) }}" required>
                            @error('date_inscription')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                @foreach($statuts as $value => $label)
                                    <option value="{{ $value }}" {{ old('statut', $apprenant->statut->value ?? $apprenant->statut) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('statut')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observations -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                    <h5 class="card-title mb-0"><i class="fas fa-sticky-note me-2"></i> Observations</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations" rows="4" placeholder="Notes éventuelles sur l'apprenant...">{{ old('observations', $apprenant->observations) }}</textarea>
                    @error('observations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">
            <!-- Photo -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                    <h5 class="card-title mb-0"><i class="fas fa-camera me-2"></i> Photo de profil</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3 position-relative d-inline-block">
                        @if($apprenant->photo_url)
                            <img id="photoPreview" src="{{ $apprenant->photo_url }}" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" alt="Aperçu photo">
                            <div class="mt-2 text-start">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="remove_photo" name="remove_photo" value="1">
                                    <label class="form-check-label text-danger small" for="remove_photo">Supprimer la photo actuelle</label>
                                </div>
                            </div>
                        @else
                            <img id="photoPreview" src="{{ asset('images/default-avatar.png') }}" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; background-color: #f8f9fa;" alt="Aperçu photo" onerror="this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22150%22%20height%3D%22150%22%20viewBox%3D%220%200%20150%20150%22%3E%3Crect%20fill%3D%22%23e9ecef%22%20width%3D%22150%22%20height%3D%22150%22%2F%3E%3Cpath%20fill%3D%22%23adb5bd%22%20d%3D%22M75%2C45c-11.028%2C0-20%2C8.972-20%2C20s8.972%2C20%2C20%2C20s20-8.972%2C20-20S86.028%2C45%2C75%2C45z%20M75%2C95c-20.952%2C0-40%2C13.256-40%2C30v5h80v-5C115%2C108.256%2C95.952%2C95%2C75%2C95z%22%2F%3E%3C%2Fsvg%3E'">
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted text-start d-block">Changer la photo :</label>
                        <input class="form-control form-control-sm @error('photo') is-invalid @enderror" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/jpg" onchange="previewImage(this)">
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text small text-muted mt-2">Formats acceptés : JPG, PNG, WebP. Taille max : 2Mo.</div>
                    </div>
                </div>
            </div>

            <!-- Contact parent/tuteur -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white py-3" style="background-color: var(--navbar-bg);">
                    <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i> En cas d'urgence</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="contact_parent" class="form-label">Nom du parent / tuteur</label>
                        <input type="text" class="form-control @error('contact_parent') is-invalid @enderror" id="contact_parent" name="contact_parent" value="{{ old('contact_parent', $apprenant->contact_parent) }}">
                        @error('contact_parent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label for="telephone_parent" class="form-label">Téléphone d'urgence</label>
                        <input type="text" class="form-control @error('telephone_parent') is-invalid @enderror" id="telephone_parent" name="telephone_parent" value="{{ old('telephone_parent', $apprenant->telephone_parent) }}">
                        @error('telephone_parent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i> Mettre à jour
                    </button>
                    <a href="{{ route('admin.apprenants.show', $apprenant) }}" class="btn btn-outline-secondary">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('js')
<script>
    // Validation du formulaire côté client
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    // Prévisualisation de l'image
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreview').src = e.target.result;
                
                // Si on a un input pour supprimer l'image, on le décoche
                let removePhotoCheckbox = document.getElementById('remove_photo');
                if (removePhotoCheckbox) {
                    removePhotoCheckbox.checked = false;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Gérer le checkbox "Supprimer l'image"
    let removePhotoCheckbox = document.getElementById('remove_photo');
    if (removePhotoCheckbox) {
        removePhotoCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Remettre l'avatar par défaut visuellement
                document.getElementById('photoPreview').src = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22150%22%20height%3D%22150%22%20viewBox%3D%220%200%20150%20150%22%3E%3Crect%20fill%3D%22%23e9ecef%22%20width%3D%22150%22%20height%3D%22150%22%2F%3E%3Cpath%20fill%3D%22%23adb5bd%22%20d%3D%22M75%2C45c-11.028%2C0-20%2C8.972-20%2C20s8.972%2C20%2C20%2C20s20-8.972%2C20-20S86.028%2C45%2C75%2C45z%20M75%2C95c-20.952%2C0-40%2C13.256-40%2C30v5h80v-5C115%2C108.256%2C95.952%2C95%2C75%2C95z%22%2F%3E%3C%2Fsvg%3E';
                // Vider l'input file
                document.getElementById('photo').value = '';
            } else {
                // Remettre l'image originale
                document.getElementById('photoPreview').src = "{{ $apprenant->photo_url ?? '' }}";
            }
        });
    }
</script>
@endsection
