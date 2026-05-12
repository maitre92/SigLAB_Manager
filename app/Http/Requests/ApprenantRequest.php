<?php

namespace App\Http\Requests;

use App\Shared\Enums\ApprenantStatut;
use App\Shared\Enums\NiveauEtude;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApprenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $apprenantId = $this->route('apprenant')?->id ?? $this->input('apprenant_id');
        $isUpdate = $this->isMethod('PUT') || $this->input('_method') === 'PUT';

        $rules = [
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'sexe' => ['required', 'string', Rule::in(['M', 'F'])],
            'date_naissance' => ['nullable', 'date', 'before:today'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('apprenants')->ignore($apprenantId),
            ],
            'adresse' => ['nullable', 'string', 'max:1000'],
            'niveau_etude' => ['required', 'string', Rule::in(array_map(fn($e) => $e->value, NiveauEtude::cases()))],
            'profession' => ['nullable', 'string', 'max:255'],
            'date_inscription' => ['required', 'date'],
            'statut' => ['required', 'string', Rule::in(array_map(fn($e) => $e->value, ApprenantStatut::cases()))],
            'contact_parent' => ['nullable', 'string', 'max:255'],
            'telephone_parent' => ['nullable', 'string', 'max:20'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'remove_photo' => ['nullable', 'boolean'],
        ];

        // Photo : obligatoire seulement si souhaité, toujours validée en format/taille
        if ($isUpdate) {
            $rules['photo'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'];
        } else {
            $rules['photo'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères.',
            'nom.required' => 'Le nom est obligatoire.',
            'nom.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'sexe.required' => 'Le sexe est obligatoire.',
            'sexe.in' => 'Le sexe doit être Masculin (M) ou Féminin (F).',
            'date_naissance.date' => 'La date de naissance n\'est pas valide.',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'telephone.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre apprenant.',
            'adresse.max' => 'L\'adresse ne doit pas dépasser 1000 caractères.',
            'niveau_etude.required' => 'Le niveau d\'étude est obligatoire.',
            'niveau_etude.in' => 'Le niveau d\'étude sélectionné n\'est pas valide.',
            'profession.max' => 'La profession ne doit pas dépasser 255 caractères.',
            'date_inscription.required' => 'La date d\'inscription est obligatoire.',
            'date_inscription.date' => 'La date d\'inscription n\'est pas valide.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'contact_parent.max' => 'Le nom du contact ne doit pas dépasser 255 caractères.',
            'telephone_parent.max' => 'Le téléphone du parent/tuteur ne doit pas dépasser 20 caractères.',
            'observations.max' => 'Les observations ne doivent pas dépasser 5000 caractères.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'La photo doit être au format JPEG, PNG, JPG ou WebP.',
            'photo.max' => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }
}
