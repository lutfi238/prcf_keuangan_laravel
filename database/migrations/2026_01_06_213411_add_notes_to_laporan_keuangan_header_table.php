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
        Schema::table('laporan_keuangan_header', function (Blueprint $table) {
            $table->text('notes_sa')->nullable()->after('status_lap');
            $table->text('notes_fm')->nullable()->after('notes_sa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_keuangan_header', function (Blueprint $table) {
            $table->dropColumn(['notes_sa', 'notes_fm']);
        });
    }
};
