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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->onDelete('cascade');
            $table->decimal('montant', 12, 2);
            $table->date('date_paiement');
            $table->string('mode_paiement', 50); // especes, wave, orange_money, virement, etc.
            $table->string('reference')->nullable(); // Numéro de transaction ou chèque
            $table->string('recu_numero')->unique();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('paiements');
    }
};
