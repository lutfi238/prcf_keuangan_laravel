<?php

use App\Enums\ProposalStatus;
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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id('id_proposal');
            $table->string('judul_proposal');
            $table->string('pj')->comment('Penanggung Jawab');
            $table->date('date')->nullable();
            $table->string('pemohon')->nullable();
            $table->enum('status', ProposalStatus::values())->default('draft')
                ->comment('draft=PM draft, submitted=waiting FM, approved_fm=FM approved (final), approved=FM approved (final), rejected=rejected');
            $table->unsignedBigInteger('approved_by_fm')->nullable();
            $table->dateTime('fm_approval_date')->nullable();
            $table->string('kode_proyek', 50)->nullable();
            $table->text('tor')->nullable()->comment('Terms of Reference');
            $table->string('file_budget')->nullable();
            $table->decimal('total_budget_usd', 15, 2)->default(0);
            $table->decimal('total_budget_idr', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->decimal('exrate_at_submission', 10, 4)->default(1.0000)->comment('Exchange rate saat submit proposal');
            $table->timestamps();

            $table->foreign('approved_by_fm')->references('id_user')->on('users')->nullOnDelete();
            $table->foreign('kode_proyek')->references('kode_proyek')->on('proyek')->nullOnDelete();
        });

        Schema::create('proposal_budget_details', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_proposal');
            $table->unsignedBigInteger('id_village');
            $table->string('exp_code', 20);
            $table->string('place_code', 50)->comment('Auto-filled dari project_code_budgets');
            $table->decimal('requested_usd', 15, 2)->default(0);
            $table->decimal('requested_idr', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD')->comment('Currency proposal: USD atau IDR');
            $table->decimal('exrate', 10, 4)->default(1.0000)->comment('Exchange rate saat proposal dibuat');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_proposal')->references('id_proposal')->on('proposals')->onDelete('cascade');
            $table->foreign('id_village')->references('id_village')->on('villages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_budget_details');
        Schema::dropIfExists('proposals');
    }
};