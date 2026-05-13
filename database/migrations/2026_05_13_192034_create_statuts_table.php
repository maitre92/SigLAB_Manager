<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statuts', function (Blueprint $table) {
            $table->id();
            $table->string('contexte', 80);
            $table->string('code', 80);
            $table->string('libelle');
            $table->string('couleur', 30)->default('secondary');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();

            $table->unique(['contexte', 'code']);
            $table->index(['contexte', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuts');
    }
};
