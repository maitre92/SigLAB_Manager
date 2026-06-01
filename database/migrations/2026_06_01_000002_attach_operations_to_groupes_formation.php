<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->foreignId('groupe_formation_id')->nullable()->after('formation_id')->constrained('groupes_formation')->nullOnDelete();
            $table->index('groupe_formation_id');
        });

        Schema::table('presences', function (Blueprint $table) {
            $table->foreignId('groupe_formation_id')->nullable()->after('formation_id')->constrained('groupes_formation')->cascadeOnDelete();
            $table->index('groupe_formation_id');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignId('groupe_formation_id')->nullable()->after('formation_id')->constrained('groupes_formation')->cascadeOnDelete();
            $table->index('groupe_formation_id');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->foreignId('groupe_formation_id')->nullable()->after('evaluation_id')->constrained('groupes_formation')->nullOnDelete();
            $table->index('groupe_formation_id');
        });

        Schema::table('attestations', function (Blueprint $table) {
            $table->foreignId('groupe_formation_id')->nullable()->after('formation_id')->constrained('groupes_formation')->cascadeOnDelete();
            $table->index('groupe_formation_id');
        });

        Schema::table('depenses', function (Blueprint $table) {
            $table->foreignId('groupe_formation_id')->nullable()->after('formation_id')->constrained('groupes_formation')->nullOnDelete();
            $table->index('groupe_formation_id');
        });

        $this->copyFormationLinksToGroups();
        $this->replaceLegacyUniqueIndexes();
    }

    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('groupe_formation_id');
        });

        Schema::table('attestations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('groupe_formation_id');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('groupe_formation_id');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('groupe_formation_id');
        });

        Schema::table('presences', function (Blueprint $table) {
            $table->dropConstrainedForeignId('groupe_formation_id');
        });

        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('groupe_formation_id');
        });
    }

    private function copyFormationLinksToGroups(): void
    {
        DB::table('groupes_formation')
            ->select('id', 'formation_id')
            ->orderBy('id')
            ->get()
            ->each(function ($groupe) {
                DB::table('inscriptions')
                    ->where('formation_id', $groupe->formation_id)
                    ->whereNull('groupe_formation_id')
                    ->update(['groupe_formation_id' => $groupe->id]);

                DB::table('presences')
                    ->where('formation_id', $groupe->formation_id)
                    ->whereNull('groupe_formation_id')
                    ->update(['groupe_formation_id' => $groupe->id]);

                DB::table('evaluations')
                    ->where('formation_id', $groupe->formation_id)
                    ->whereNull('groupe_formation_id')
                    ->update(['groupe_formation_id' => $groupe->id]);

                DB::table('attestations')
                    ->where('formation_id', $groupe->formation_id)
                    ->whereNull('groupe_formation_id')
                    ->update(['groupe_formation_id' => $groupe->id]);

                DB::table('depenses')
                    ->where('formation_id', $groupe->formation_id)
                    ->whereNull('groupe_formation_id')
                    ->update(['groupe_formation_id' => $groupe->id]);
            });

        DB::table('evaluations')
            ->join('notes', 'notes.evaluation_id', '=', 'evaluations.id')
            ->whereNull('notes.groupe_formation_id')
            ->update(['notes.groupe_formation_id' => DB::raw('evaluations.groupe_formation_id')]);
    }

    private function replaceLegacyUniqueIndexes(): void
    {
        try {
            DB::statement('ALTER TABLE inscriptions DROP INDEX unique_inscription');
        } catch (Throwable $e) {
            //
        }

        try {
            DB::statement('ALTER TABLE inscriptions ADD UNIQUE unique_apprenant_groupe (apprenant_id, groupe_formation_id)');
        } catch (Throwable $e) {
            //
        }

        try {
            DB::statement('ALTER TABLE presences DROP INDEX presences_formation_id_apprenant_id_date_unique');
        } catch (Throwable $e) {
            //
        }

        try {
            DB::statement('ALTER TABLE presences ADD UNIQUE unique_presence_groupe_jour (groupe_formation_id, apprenant_id, date)');
        } catch (Throwable $e) {
            //
        }
    }
};
