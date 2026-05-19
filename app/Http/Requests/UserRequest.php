<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Shared\Enums\UserRole;
use App\Shared\Enums\UserStatus;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->input('user_id');
        $isUpdate = $this->isMethod('PUT') || $this->input('_method') === 'PUT';
        $assignableRoles = array_map(
            fn(UserRole $role) => $role->value,
            UserRole::assignableBy($this->user())
        );
        $diplomesFormateur = ['DUT', 'BTS', 'LICENCE', 'MASTER', 'DEA', 'DOCTORAT', 'AUTRE'];
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'specialite' => ['nullable', 'required_if:role,' . UserRole::FORMATEUR->value, 'string', 'max:255'],
            'diplome' => ['nullable', 'required_if:role,' . UserRole::FORMATEUR->value, 'string', Rule::in($diplomesFormateur)],
            'adresse' => ['nullable', 'required_if:role,' . UserRole::FORMATEUR->value, 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in($assignableRoles)],
            'status' => ['required', 'string', Rule::in(array_map(fn($s) => $s->value, UserStatus::cases()))],
        ];
        
        if (!$isUpdate) {
            // En création : mot de passe obligatoire
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            // En modification : mot de passe optionnel
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }
        
        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Vous ne pouvez attribuer que les rôles autorisés par votre niveau.',
            'status.required' => 'Le statut est obligatoire.',
            'specialite.required_if' => 'La spécialité est obligatoire pour un formateur.',
            'diplome.required_if' => 'Le diplôme est obligatoire pour un formateur.',
            'diplome.in' => 'Veuillez sélectionner un diplôme valide.',
            'adresse.required_if' => 'L\'adresse est obligatoire pour un formateur.',
        ];
    }
}
