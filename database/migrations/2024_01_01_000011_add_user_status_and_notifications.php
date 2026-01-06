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
        // Create notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50)->comment('proposal_submitted, proposal_approved, report_verified, etc');
            $table->text('message');
            $table->string('link')->nullable()->comment('URL to related resource');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID of related proposal/report');
            $table->string('related_type', 50)->nullable()->comment('proposal, report, user');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
