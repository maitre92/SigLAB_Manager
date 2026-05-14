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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->string('titre');
            $table->enum('type', ['evaluation', 'examen'])->default('evaluation');
            $table->dateTime('date_evaluation');
            $table->decimal('coefficient', 5, 2)->default(1.00);
            $table->text('description')->nullable();
            $table->enum('statut', ['prevu', 'termine', 'annule'])->default('prevu');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
