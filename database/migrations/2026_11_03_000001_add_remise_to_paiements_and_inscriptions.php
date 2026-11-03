<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('paiements', 'remise')) {
            Schema::table('paiements', function (Blueprint $table) {
                $table->decimal('remise', 12, 2)->default(0)->after('montant');
            });
        }

        if (! Schema::hasColumn('inscriptions', 'montant_remise')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->decimal('montant_remise', 12, 2)->default(0)->after('montant_paye');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('paiements', 'remise')) {
            Schema::table('paiements', function (Blueprint $table) {
                $table->dropColumn('remise');
            });
        }

        if (Schema::hasColumn('inscriptions', 'montant_remise')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->dropColumn('montant_remise');
            });
        }
    }
};
