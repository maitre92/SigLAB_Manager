<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emargements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_formation_id')->constrained('groupes_formation')->cascadeOnDelete();
            $table->foreignId('formateur_id')->constrained('users')->cascadeOnDelete();
            $table->date('date_seance');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('titre_realise');
            $table->string('statut', 30)->default('en_attente');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->timestamps();

            $table->index(['groupe_formation_id', 'formateur_id']);
            $table->index(['statut', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emargements');
    }
};
