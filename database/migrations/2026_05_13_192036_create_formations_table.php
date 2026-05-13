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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->foreignId('categorie_formation_id')->nullable()->constrained('categorie_formations')->nullOnDelete();
            $table->string('type', 50);
            $table->unsignedInteger('duree_heures')->default(0);
            $table->decimal('cout', 12, 2)->default(0);
            $table->unsignedInteger('capacite_max')->nullable();
            $table->string('niveau', 100)->nullable();
            $table->string('statut', 50)->default('planifiee');
            $table->string('salle')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('emploi_du_temps')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index('nom');
            $table->index('type');
            $table->index('statut');
            $table->index(['date_debut', 'date_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
