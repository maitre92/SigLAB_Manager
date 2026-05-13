<?php

namespace Database\Seeders;

use App\Models\CategorieFormation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormationReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Réseau Informatique',
            'Programmation',
            'Microsoft Office',
            'Anglais',
            'Maintenance',
            'Cybersécurité',
        ] as $nom) {
            $categorie = CategorieFormation::withTrashed()->firstOrNew(['slug' => Str::slug($nom)]);
            $categorie->nom = $nom;
            $categorie->is_active = true;
            $categorie->save();

            if ($categorie->trashed()) {
                $categorie->restore();
            }
        }

        foreach ([
            ['code' => 'planifiee', 'libelle' => 'Planifiée', 'couleur' => 'primary', 'ordre' => 1],
            ['code' => 'en_cours', 'libelle' => 'En cours', 'couleur' => 'success', 'ordre' => 2],
            ['code' => 'terminee', 'libelle' => 'Terminée', 'couleur' => 'secondary', 'ordre' => 3],
            ['code' => 'suspendue', 'libelle' => 'Suspendue', 'couleur' => 'warning', 'ordre' => 4],
        ] as $statut) {
            DB::table('statuts')->updateOrInsert(
                ['contexte' => 'formation', 'code' => $statut['code']],
                array_merge($statut, ['contexte' => 'formation', 'is_active' => true, 'updated_at' => now(), 'created_at' => now()])
            );
        }
    }
}
