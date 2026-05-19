<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('formations', 'frais_inscription')) {
            return;
        }

        Schema::table('formations', function (Blueprint $table) {
            $table->decimal('frais_inscription', 12, 2)->default(0)->after('cout');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('formations', 'frais_inscription')) {
            return;
        }

        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn('frais_inscription');
        });
    }
};
