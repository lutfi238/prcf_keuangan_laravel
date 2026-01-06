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
        Schema::create('laporan_donor', function (Blueprint $table) {
            $table->id('id_donor');
            $table->string('periode', 50)->nullable();
            $table->string('kode_proyek', 50)->nullable();
            $table->text('realisasi_kegiatan')->nullable();
            $table->text('realisasi_keuangan')->nullable();
            $table->decimal('total_anggaran', 15, 2)->nullable();
            $table->decimal('total_realisasi', 15, 2)->nullable();
            $table->string('file_laporan')->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'sent'])->default('draft');
            $table->timestamps();

            $table->foreign('kode_proyek')->references('kode_proyek')->on('proyek')->nullOnDelete();
            $table->foreign('created_by')->references('id_user')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_donor');
    }
};