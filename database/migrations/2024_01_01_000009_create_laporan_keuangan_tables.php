<?php

use App\Enums\ReportStatus;
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
        Schema::create('laporan_keuangan_header', function (Blueprint $table) {
            $table->id('id_laporan_keu');
            $table->string('kode_projek', 50)->nullable();
            $table->string('nama_projek')->nullable();
            $table->string('nama_kegiatan')->nullable();
            $table->string('pelaksana')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->date('tanggal_laporan')->nullable();
            $table->string('mata_uang', 10)->default('IDR');
            $table->decimal('exrate', 10, 4)->default(1.0000);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->enum('status_lap', ReportStatus::values())->default('draft')
                ->comment('draft=PM draft, submitted=waiting SA, verified=SA verified, approved=FM approved, rejected=rejected, revision_requested=FM requested revision');
            $table->text('catatan_finance')->nullable();
            $table->timestamps();

            $table->foreign('kode_projek')->references('kode_proyek')->on('proyek')->nullOnDelete();
            $table->foreign('created_by')->references('id_user')->on('users')->nullOnDelete();
            $table->foreign('verified_by')->references('id_user')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id_user')->on('users')->nullOnDelete();
        });

        Schema::create('laporan_keuangan_detail', function (Blueprint $table) {
            $table->id('id_detail_keu');
            $table->unsignedBigInteger('id_laporan_keu');
            $table->string('invoice_no', 100)->nullable();
            $table->date('invoice_date')->nullable();
            $table->text('item_desc')->nullable();
            $table->string('recipient')->nullable();
            $table->string('place_code', 50)->nullable();
            $table->string('exp_code', 50)->nullable();
            $table->integer('unit_total')->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('requested', 15, 2)->nullable();
            $table->decimal('actual', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->text('explanation')->nullable();
            $table->string('file_nota')->nullable();
            $table->timestamps();

            $table->foreign('id_laporan_keu')->references('id_laporan_keu')->on('laporan_keuangan_header')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_keuangan_detail');
        Schema::dropIfExists('laporan_keuangan_header');
    }
};