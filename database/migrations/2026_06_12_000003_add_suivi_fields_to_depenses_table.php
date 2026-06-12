<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->decimal('montant_commission_initial', 12, 2)->nullable()->after('montant');
            $table->decimal('montant_retranchement', 12, 2)->default(0)->after('montant_commission_initial');
            $table->decimal('heures_prevues', 8, 2)->nullable()->after('montant_retranchement');
            $table->decimal('heures_validees', 8, 2)->nullable()->after('heures_prevues');
            $table->text('motif_retranchement')->nullable()->after('heures_validees');
            $table->foreignId('retranchement_defined_by')->nullable()->after('motif_retranchement')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('retranchement_defined_by');
            $table->dropColumn([
                'montant_commission_initial',
                'montant_retranchement',
                'heures_prevues',
                'heures_validees',
                'motif_retranchement',
            ]);
        });
    }
};
