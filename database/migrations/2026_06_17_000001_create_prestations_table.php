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
        Schema::create('prestations', function (Blueprint $table) {
            $table->id();
            $table->string('titre'); // Ex: "Réparation ordinateur", "Installation logiciel"
            $table->string('client_nom'); // Nom du client
            $table->string('client_telephone')->nullable(); // Téléphone du client
            $table->text('description')->nullable(); // Description détaillée du travail
            $table->decimal('montant', 12, 2); // Montant facturé
            $table->date('date_prestation'); // Date de la prestation
            $table->string('mode_paiement')->default('espèces'); // espèces, wave, orange_money, etc.
            $table->string('reference')->nullable(); // Référence de transaction
            $table->string('recu_numero')->unique(); // Numéro de reçu unique (PREST-YYYY-XXXXX)
            $table->enum('statut', ['en_cours', 'terminee', 'livree'])->default('terminee');
            $table->text('notes')->nullable(); // Notes complémentaires
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestations');
    }
};
