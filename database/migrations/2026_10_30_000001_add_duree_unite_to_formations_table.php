<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('formations', 'duree_unite')) {
            return;
        }

        Schema::table('formations', function (Blueprint $table) {
            $table->string('duree_unite', 10)->default('heures')->after('duree_heures');
            $table->unsignedSmallInteger('duree_mois')->nullable()->after('duree_unite');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('formations', 'duree_unite')) {
            return;
        }

        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn(['duree_unite', 'duree_mois']);
        });
    }
};
