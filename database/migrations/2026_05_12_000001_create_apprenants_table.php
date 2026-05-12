<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apprenants', function (Blueprint $table) {
            $table->id();

            // Matricule unique auto-généré (SIG-2026-0001)
            $table->string('matricule', 20)->unique();

            // Informations personnelles
            $table->string('prenom');
            $table->string('nom');
            $table->string('photo')->nullable();
            $table->enum('sexe', ['M', 'F']);
            $table->date('date_naissance')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();

            // Informations académiques
            $table->enum('niveau_etude', [
                'aucun', 'primaire', 'secondaire', 'baccalaureat',
                'licence', 'master', 'doctorat', 'autre'
            ])->default('aucun');
            $table->string('profession')->nullable();
            $table->date('date_inscription');
            $table->enum('statut', [
                'actif', 'inactif', 'suspendu', 'diplome', 'abandonne'
            ])->default('actif');

            // Contact parent/tuteur
            $table->string('contact_parent')->nullable();
            $table->string('telephone_parent', 20)->nullable();

            // Observations
            $table->text('observations')->nullable();

            // Créé par (utilisateur qui a enregistré l'apprenant)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // Soft delete + timestamps
            $table->softDeletes();
            $table->timestamps();

            // Index pour les recherches fréquentes
            $table->index('statut');
            $table->index('date_inscription');
            $table->index(['nom', 'prenom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apprenants');
    }
};
