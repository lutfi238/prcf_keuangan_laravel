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
        Schema::create('buku_bank_header', function (Blueprint $table) {
            $table->string('id_bank_header', 30)->primary();
            $table->string('kode_proyek', 50);
            $table->string('account_name', 150)->default('');
            $table->string('bank_name', 100)->default('');
            $table->string('account_number', 50)->default('');
            $table->decimal('exrate', 12, 2)->default(1.00);
            $table->string('currency', 10)->default('');
            $table->char('periode_bulan', 2);
            $table->char('periode_tahun', 4);
            $table->decimal('saldo_awal_idr', 18, 2)->default(0);
            $table->decimal('saldo_awal_usd', 18, 2)->default(0);
            $table->decimal('current_period_change_idr', 18, 2)->default(0);
            $table->decimal('current_period_change_usd', 18, 2)->default(0);
            $table->decimal('saldo_akhir_idr', 18, 2)->default(0);
            $table->decimal('saldo_akhir_usd', 18, 2)->default(0);
            $table->string('prepared_by', 100)->nullable();
            $table->string('approved_by', 100)->nullable();
            $table->enum('status_laporan', BankBookStatus::values())->default('draft');
            $table->date('tanggal_pembuatan')->useCurrent();
            $table->date('tanggal_persetujuan')->nullable();

            $table->foreign('kode_proyek')->references('kode_proyek')->on('proyek')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('buku_bank_detail', function (Blueprint $table) {
            $table->string('id_detail_bank', 30)->primary();
            $table->string('id_bank_header', 30);
            $table->date('tanggal');
            $table->string('reff', 50)->nullable();
            $table->string('title_activity', 150)->nullable();
            $table->text('cost_description')->nullable();
            $table->string('recipient', 100)->nullable();
            $table->string('place_code', 20)->nullable();
            $table->string('exp_code', 20)->nullable();
            $table->string('nominal_code', 20)->nullable();
            $table->decimal('exrate', 12, 2)->nullable();
            $table->string('cost_curr', 10)->nullable();
            $table->decimal('debit_idr', 18, 2)->default(0);
            $table->decimal('debit_usd', 18, 2)->default(0);
            $table->decimal('credit_idr', 18, 2)->default(0);
            $table->decimal('credit_usd', 18, 2)->default(0);
            $table->decimal('balance_idr', 18, 2)->default(0);
            $table->decimal('balance_usd', 18, 2)->default(0);
            $table->enum('status', ['ongoing', 'final'])->default('ongoing');

            $table->foreign('id_bank_header')->references('id_bank_header')->on('buku_bank_header')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_bank_detail');
        Schema::dropIfExists('buku_bank_header');
    }
};