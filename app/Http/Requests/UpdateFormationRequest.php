<?php

namespace App\Http\Requests;

use App\Shared\Enums\FormationStatut;
use App\Shared\Enums\FormationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $formationId = $this->route('formation')?->id;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('formations', 'code')->ignore($formationId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'categorie_formation_id' => ['nullable', 'exists:categorie_formations,id'],
            'type' => ['required', Rule::in(array_map(fn($type) => $type->value, FormationType::cases()))],
            'duree_heures' => ['required', 'integer', 'min:1', 'max:10000'],
            'cout' => ['required', 'numeric', 'min:0', 'max:999999999.99'],
            'capacite_max' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'niveau' => ['nullable', 'string', 'max:100'],
            'statut' => ['required', Rule::in(array_map(fn($statut) => $statut->value, FormationStatut::cases()))],
            'salle' => ['nullable', 'string', 'max:255'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'emploi_du_temps' => ['nullable', 'string', 'max:5000'],
            'formateurs' => ['nullable', 'array'],
            'formateurs.*' => ['integer', 'exists:users,id'],
        ];
    }
}
