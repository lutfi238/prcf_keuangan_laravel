<?php

use App\Enums\BankBookStatus;
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
        Schema::create('buku_piutang_header', function (Blueprint $table) {
            $table->id('id_piutang');
            $table->string('kode_proyek', 50)->nullable();
            $table->char('periode_bulan', 2);
            $table->char('periode_tahun', 4);
            $table->decimal('exrate', 12, 2)->default(1.00);
            $table->decimal('beginning_balance_idr', 15, 2)->default(0);
            $table->decimal('ending_balance_idr', 15, 2)->default(0);
            $table->decimal('beginning_balance_usd', 15, 2)->default(0);
            $table->decimal('ending_balance_usd', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('catatan_fm')->nullable();
            $table->enum('status', BankBookStatus::values())->default('draft');
            $table->date('tgl_pembuatan')->nullable();
            $table->date('tgl_persetujuan')->nullable();
            $table->timestamps();

            $table->foreign('kode_proyek')->references('kode_proyek')->on('proyek')->nullOnDelete();
            $table->foreign('created_by')->references('id_user')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id_user')->on('users')->nullOnDelete();
        });

        Schema::create('buku_piutang_detail', function (Blueprint $table) {
            $table->id('id_detail_piutang');
            $table->unsignedBigInteger('id_piutang');
            $table->date('tgl_trx')->nullable();
            $table->string('reff', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('recipient')->nullable();
            $table->string('p_code', 50)->nullable();
            $table->string('exp_code', 50)->nullable();
            $table->string('nominal_code', 50)->nullable();
            $table->decimal('exrate', 10, 4)->default(1.0000);
            $table->decimal('debit_idr', 15, 2)->default(0);
            $table->decimal('debit_usd', 15, 2)->default(0);
            $table->decimal('credit_idr', 15, 2)->default(0);
            $table->decimal('credit_usd', 15, 2)->default(0);
            $table->decimal('balance_idr', 15, 2)->default(0);
            $table->decimal('balance_usd', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_piutang')->references('id_piutang')->on('buku_piutang_header')->onDelete('cascade');
        });

        Schema::create('buku_piutang_unliquidated', function (Blueprint $table) {
            $table->id('id_unliquidate');
            $table->unsignedBigInteger('id_piutang');
            $table->date('tgl')->nullable();
            $table->string('voucher_no', 100)->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('nilai_idr', 15, 2)->default(0);
            $table->decimal('nilai_usd', 15, 2)->default(0);
            $table->enum('status', ['pending', 'liquidated', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->foreign('id_piutang')->references('id_piutang')->on('buku_piutang_header')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_piutang_unliquidated');
        Schema::dropIfExists('buku_piutang_detail');
        Schema::dropIfExists('buku_piutang_header');
    }
};