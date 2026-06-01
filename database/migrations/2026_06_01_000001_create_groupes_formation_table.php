<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groupes_formation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->string('nom');
            $table->string('code', 80)->unique();
            $table->foreignId('formateur_principal_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('statut', 50)->default('planifiee');
            $table->unsignedInteger('capacite_max')->nullable();
            $table->string('salle')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('emploi_du_temps')->nullable();
            $table->text('observations')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('formation_id');
            $table->index('formateur_principal_id');
            $table->index('statut');
        });

        Schema::create('groupe_formation_formateur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_formation_id')->constrained('groupes_formation')->cascadeOnDelete();
            $table->foreignId('formateur_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 50)->default('intervenant');
            $table->unsignedTinyInteger('taux_commission')->nullable();
            $table->text('observations')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['groupe_formation_id', 'formateur_id'], 'unique_groupe_formateur');
            $table->index('formateur_id');
        });

        $now = now();

        DB::table('formations')
            ->orderBy('id')
            ->get()
            ->each(function ($formation) use ($now) {
                $firstTrainer = DB::table('formation_formateur')
                    ->where('formation_id', $formation->id)
                    ->orderBy('id')
                    ->first();

                $groupeId = DB::table('groupes_formation')->insertGetId([
                    'formation_id' => $formation->id,
                    'nom' => $formation->nom . ' G1',
                    'code' => $formation->code . '-G1',
                    'formateur_principal_id' => $firstTrainer?->user_id,
                    'statut' => $formation->statut,
                    'capacite_max' => $formation->capacite_max,
                    'salle' => $formation->salle,
                    'date_debut' => $formation->date_debut,
                    'date_fin' => $formation->date_fin,
                    'emploi_du_temps' => $formation->emploi_du_temps,
                    'created_by' => $formation->created_by,
                    'created_at' => $formation->created_at ?? $now,
                    'updated_at' => $formation->updated_at ?? $now,
                ]);

                DB::table('formation_formateur')
                    ->where('formation_id', $formation->id)
                    ->orderBy('id')
                    ->get()
                    ->each(function ($formateur) use ($groupeId, $firstTrainer, $now) {
                        DB::table('groupe_formation_formateur')->insert([
                            'groupe_formation_id' => $groupeId,
                            'formateur_id' => $formateur->user_id,
                            'role' => $firstTrainer && $firstTrainer->user_id === $formateur->user_id ? 'principal' : 'intervenant',
                            'taux_commission' => $formateur->pourcentage_commission,
                            'assigned_at' => $formateur->assigned_at ?? $now,
                            'created_at' => $formateur->created_at ?? $now,
                            'updated_at' => $formateur->updated_at ?? $now,
                        ]);
                    });
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('groupe_formation_formateur');
        Schema::dropIfExists('groupes_formation');
    }
};
