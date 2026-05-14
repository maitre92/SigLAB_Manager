<?php

namespace Database\Seeders;

use App\Models\Apprenant;
use App\Models\Formation;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\Paiement;
use App\Models\Depense;
use App\Models\Inscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedagogieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        if (!$admin) return;
        $adminId = $admin->id;

        // Nettoyage préalable
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Paiement::truncate();
        Depense::truncate();
        DB::table('presences')->truncate();
        DB::table('notes')->truncate();
        Evaluation::truncate();
        Inscription::truncate();
        Formation::truncate();
        Apprenant::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Création d'apprenants diversifiés
        $apprenantsData = [
            ['prenom' => 'Jean', 'nom' => 'Dupont', 'sexe' => 'M', 'email' => 'jean@example.com'],
            ['prenom' => 'Marie', 'nom' => 'Traoré', 'sexe' => 'F', 'email' => 'marie@example.com'],
            ['prenom' => 'Abdoulaye', 'nom' => 'Koné', 'sexe' => 'M', 'email' => 'abdou@example.com'],
            ['prenom' => 'Fatoumata', 'nom' => 'Diallo', 'sexe' => 'F', 'email' => 'fatou@example.com'],
            ['prenom' => 'Sekou', 'nom' => 'Coulibaly', 'sexe' => 'M', 'email' => 'sekou@example.com'],
            ['prenom' => 'Alassane', 'nom' => 'Ouattara', 'sexe' => 'M', 'email' => 'alassane@example.com'],
            ['prenom' => 'Kadiatou', 'nom' => 'Sow', 'sexe' => 'F', 'email' => 'kadi@example.com'],
            ['prenom' => 'Moussa', 'nom' => 'Dembélé', 'sexe' => 'M', 'email' => 'moussa@example.com'],
            ['prenom' => 'Awa', 'nom' => 'Ndiaye', 'sexe' => 'F', 'email' => 'awa@example.com'],
            ['prenom' => 'Ousmane', 'nom' => 'Sarr', 'sexe' => 'M', 'email' => 'ousmane@example.com'],
        ];

        $apprenants = [];
        foreach ($apprenantsData as $data) {
            $apprenants[] = Apprenant::create(array_merge($data, [
                'matricule' => Apprenant::generateMatricule(),
                'date_inscription' => now()->subDays(rand(1, 60)),
                'statut' => 'actif',
                'created_by' => $adminId
            ]));
        }

        // 2. Création de formations
        $formationsData = [
            [
                'nom' => 'Développement Web Fullstack',
                'description' => 'Maîtrise de PHP, Laravel et React',
                'cout' => 350000,
                'statut' => 'en_cours',
                'date_debut' => now()->subMonths(1),
                'date_fin' => now()->addMonths(3),
            ],
            [
                'nom' => 'Data Science & IA',
                'description' => 'Python, Pandas, Scikit-learn',
                'cout' => 450000,
                'statut' => 'planifiee',
                'date_debut' => now()->addDays(15),
                'date_fin' => now()->addMonths(5),
            ],
            [
                'nom' => 'Design Graphique PRO',
                'description' => 'Photoshop, Illustrator, InDesign',
                'cout' => 200000,
                'statut' => 'terminee',
                'date_debut' => now()->subMonths(4),
                'date_fin' => now()->subDays(10),
            ]
        ];

        $formations = [];
        foreach ($formationsData as $fData) {
            $formations[] = Formation::create(array_merge($fData, [
                'code' => 'FOR-' . rand(100, 999),
                'type' => 'Technique',
                'duree_heures' => 120,
                'capacite_max' => 25,
                'created_by' => $adminId
            ]));
        }

        // 3. Inscriptions & Paiements
        foreach ($apprenants as $index => $app) {
            // Inscription à la formation Web
            $ins = Inscription::create([
                'apprenant_id' => $app->id,
                'formation_id' => $formations[0]->id,
                'date_inscription' => now()->subMonths(1),
                'montant_total' => $formations[0]->cout,
                'montant_paye' => 0,
                'statut' => 'validee',
                'created_by' => $adminId
            ]);

            // Quelques paiements pour chaque inscription
            $versement = rand(50000, 150000);
            Paiement::create([
                'inscription_id' => $ins->id,
                'montant' => $versement,
                'date_paiement' => now()->subDays(rand(1, 20)),
                'mode_paiement' => 'wave',
                'recu_numero' => Paiement::generateRecuNumero(),
                'created_by' => $adminId
            ]);
            $ins->increment('montant_paye', $versement);

            // Inscription aléatoire à une autre formation pour certains
            if ($index % 3 == 0) {
                $fIndex = rand(1, 2);
                $ins2 = Inscription::create([
                    'apprenant_id' => $app->id,
                    'formation_id' => $formations[$fIndex]->id,
                    'date_inscription' => now()->subDays(5),
                    'montant_total' => $formations[$fIndex]->cout,
                    'montant_paye' => 0,
                    'statut' => 'validee',
                    'created_by' => $adminId
                ]);
                
                $v2 = rand(20000, 50000);
                Paiement::create([
                    'inscription_id' => $ins2->id,
                    'montant' => $v2,
                    'date_paiement' => now(),
                    'mode_paiement' => 'espèces',
                    'recu_numero' => Paiement::generateRecuNumero(),
                    'created_by' => $adminId
                ]);
                $ins2->increment('montant_paye', $v2);
            }
        }

        // 4. Dépenses fictives
        $depenses = [
            ['titre' => 'Loyer du centre - Mai', 'categorie' => 'Loyer', 'montant' => 150000, 'date_depense' => now()->subDays(10)],
            ['titre' => 'Facture Électricité SENELEC', 'categorie' => 'Électricité/Eau', 'montant' => 45000, 'date_depense' => now()->subDays(5)],
            ['titre' => 'Achat de 5 souris sans fil', 'categorie' => 'Matériel informatique', 'montant' => 25000, 'date_depense' => now()->subDays(2)],
            ['titre' => 'Salaire formateur PHP', 'categorie' => 'Salaire', 'montant' => 100000, 'date_depense' => now()->subDays(15)],
            ['titre' => 'Publicité Facebook Ads', 'categorie' => 'Marketing/Publicité', 'montant' => 30000, 'date_depense' => now()->subDays(20)],
        ];

        foreach ($depenses as $d) {
            Depense::create(array_merge($d, ['created_by' => $adminId]));
        }

        // 5. Évaluations
        Evaluation::create([
            'titre' => 'Test de base HTML/CSS',
            'type' => 'evaluation',
            'date_evaluation' => now()->subWeeks(2),
            'coefficient' => 1,
            'formation_id' => $formations[0]->id,
            'statut' => 'termine'
        ]);

        Evaluation::create([
            'titre' => 'Projet JavaScript DOM',
            'type' => 'examen',
            'date_evaluation' => now()->addDays(5),
            'coefficient' => 2,
            'formation_id' => $formations[0]->id,
            'statut' => 'prevu'
        ]);
    }
}
