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
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('categorie', 100); // Loyer, Salaire, Materiel, Marketing, Electricite, etc.
            $table->decimal('montant', 12, 2);
            $table->date('date_depense');
            $table->string('beneficiaire')->nullable();
            $table->text('description')->nullable();
            $table->string('piece_jointe')->nullable(); // Path to invoice scan
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
        Schema::dropIfExists('depenses');
    }
};
