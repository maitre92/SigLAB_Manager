<?php

namespace App\Http\Requests;

use App\Shared\Enums\FormationStatut;
use App\Shared\Enums\FormationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:formations,code'],
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

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la formation est obligatoire.',
            'code.unique' => 'Ce code formation est déjà utilisé.',
            'type.required' => 'Le type de formation est obligatoire.',
            'duree_heures.required' => 'La durée est obligatoire.',
            'duree_heures.min' => 'La durée doit être supérieure à zéro.',
            'cout.required' => 'Le coût est obligatoire.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ];
    }
}
