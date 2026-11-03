<?php

namespace Tests\Feature;

use App\Models\Apprenant;
use App\Models\CategorieFormation;
use App\Models\Formation;
use App\Models\GroupeFormation;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementRemiseTest extends TestCase
{
    use RefreshDatabase;

    private Formation $formation;
    private Inscription $inscription;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::factory()->create(['role' => UserRole::SUPERADMIN->value]);
        $this->actingAs($admin);

        $categorie = CategorieFormation::create(['nom' => 'Informatique', 'is_active' => true]);
        $this->formation = Formation::create([
            'nom' => 'Web',
            'code' => 'WEB-1',
            'categorie_formation_id' => $categorie->id,
            'type' => 'Présentiel',
            'cout' => 90000,
            'frais_inscription' => 10000,
        ]);
        $groupe = GroupeFormation::create([
            'formation_id' => $this->formation->id,
            'nom' => 'Groupe A',
            'code' => 'GRP-A',
        ]);
        $apprenant = Apprenant::create([
            'matricule' => 'SIG-TEST-1',
            'prenom' => 'Awa',
            'nom' => 'Test',
            'sexe' => 'F',
            'date_inscription' => now(),
            'statut' => 'actif',
        ]);
        $this->inscription = Inscription::create([
            'apprenant_id' => $apprenant->id,
            'formation_id' => $this->formation->id,
            'groupe_formation_id' => $groupe->id,
            'date_inscription' => now(),
            'montant_total' => 100000,
            'statut' => 'en_attente',
        ]);
    }

    private function payer(float $montant, ?float $remise = null)
    {
        return $this->post(route('admin.finances.payments.store'), [
            'inscription_id' => $this->inscription->id,
            'montant' => $montant,
            'remise' => $remise,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'wave',
        ]);
    }

    private function assertInscription(float $net, float $paye, float $remise): void
    {
        $this->inscription->refresh();
        $this->assertEquals($net, (float) $this->inscription->montant_total);
        $this->assertEquals($paye, (float) $this->inscription->montant_paye);
        $this->assertEquals($remise, (float) $this->inscription->montant_remise);
    }

    public function test_paiement_sans_remise_reste_inchange(): void
    {
        $this->payer(30000)->assertSessionHasNoErrors();

        $this->assertInscription(100000, 30000, 0);
        $this->assertEquals(70000, $this->inscription->reste_a_payer);
    }

    public function test_remise_reduit_le_reste_sans_compter_comme_recette(): void
    {
        $this->payer(30000, 20000)->assertSessionHasNoErrors();

        $this->assertInscription(80000, 30000, 20000);
        $this->assertEquals(50000, $this->inscription->reste_a_payer);
        $this->assertEquals(100000, $this->inscription->montant_brut);
        $this->assertEquals(30000, Paiement::sum('montant'));
        $this->assertSame('validee', $this->inscription->statut);
    }

    public function test_montant_et_remise_ne_peuvent_pas_depasser_le_reste(): void
    {
        $this->payer(90000, 20000)->assertSessionHasErrors('montant');

        $this->assertInscription(100000, 0, 0);
        $this->assertSame(0, Paiement::count());
    }

    public function test_il_faut_un_montant_ou_une_remise(): void
    {
        $this->payer(0)->assertSessionHasErrors('montant');
        $this->assertSame(0, Paiement::count());
    }

    public function test_remise_negative_refusee(): void
    {
        $this->payer(1000, -500)->assertSessionHasErrors('remise');
        $this->assertSame(0, Paiement::count());
    }

    public function test_remise_seule_partielle_ne_valide_pas_l_inscription(): void
    {
        $this->payer(0, 20000)->assertSessionHasNoErrors();

        $this->assertInscription(80000, 0, 20000);
        $this->assertSame('en_attente', $this->inscription->statut);
    }

    public function test_remise_seule_qui_solde_valide_l_inscription(): void
    {
        $this->payer(95000)->assertSessionHasNoErrors();
        $this->payer(0, 5000)->assertSessionHasNoErrors();

        $this->assertInscription(95000, 95000, 5000);
        $this->assertEquals(0, $this->inscription->reste_a_payer);
        $this->assertSame('validee', $this->inscription->statut);
    }

    public function test_modification_ajuste_la_remise(): void
    {
        $this->payer(30000, 20000);
        $paiement = Paiement::first();

        $this->put(route('admin.finances.payments.update', $paiement), [
            'montant' => 40000,
            'remise' => 5000,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'wave',
        ])->assertSessionHasNoErrors();

        $this->assertInscription(95000, 40000, 5000);
    }

    public function test_modification_peut_utiliser_le_reste_libere_par_l_ancienne_remise(): void
    {
        $this->payer(30000, 20000);
        $paiement = Paiement::first();

        $this->put(route('admin.finances.payments.update', $paiement), [
            'montant' => 100000,
            'remise' => 0,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'wave',
        ])->assertSessionHasNoErrors();

        $this->assertInscription(100000, 100000, 0);
    }

    public function test_modification_refuse_un_depassement(): void
    {
        $this->payer(30000, 20000);
        $paiement = Paiement::first();

        $this->put(route('admin.finances.payments.update', $paiement), [
            'montant' => 90000,
            'remise' => 20000,
            'date_paiement' => now()->toDateString(),
            'mode_paiement' => 'wave',
        ])->assertSessionHasErrors('montant');

        $this->assertInscription(80000, 30000, 20000);
    }

    public function test_suppression_annule_la_remise(): void
    {
        $this->payer(30000, 20000);

        $this->delete(route('admin.finances.payments.destroy', Paiement::first()))->assertSessionHasNoErrors();

        $this->assertInscription(100000, 0, 0);
    }

    public function test_changement_de_prix_conserve_la_remise(): void
    {
        $this->payer(30000, 20000);

        $this->put(route('admin.formations.update', $this->formation), [
            'nom' => $this->formation->nom,
            'code' => $this->formation->code,
            'categorie_formation_id' => $this->formation->categorie_formation_id,
            'type' => 'Présentiel',
            'duree' => 10,
            'duree_unite' => 'heures',
            'cout' => 140000,
            'frais_inscription' => 10000,
        ])->assertSessionHasNoErrors();

        $this->assertInscription(130000, 30000, 20000);
    }

    public function test_recu_affiche_le_prix_brut_et_la_remise(): void
    {
        $this->payer(30000, 20000);

        $this->get(route('admin.finances.payments.receipt', Paiement::first()))
            ->assertOk()
            ->assertSee('Remise accordée')
            ->assertSee('100 000')
            ->assertSee('50 000');
    }

    public function test_page_des_paiements_affiche_le_champ_et_la_remise(): void
    {
        $this->payer(30000, 20000);

        $this->get(route('admin.finances.payments'))
            ->assertOk()
            ->assertSee('name="remise"', false)
            ->assertSee('Remise : 20 000 FCFA');
    }
}
