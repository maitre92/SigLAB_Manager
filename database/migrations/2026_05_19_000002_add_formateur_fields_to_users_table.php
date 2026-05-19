<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('specialite')->nullable()->after('phone');
            $table->string('diplome')->nullable()->after('specialite');
            $table->string('adresse')->nullable()->after('diplome');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'specialite',
                'diplome',
                'adresse',
            ]);
        });
    }
};
