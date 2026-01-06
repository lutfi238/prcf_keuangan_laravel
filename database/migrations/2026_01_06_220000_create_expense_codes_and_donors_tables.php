<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('description', 255);
            $table->string('category', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('donors', function (Blueprint $table) {
            $table->id('id_donor');
            $table->string('kode_donor', 20)->unique();
            $table->string('nama_donor', 255);
            $table->text('alamat')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('negara', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
        Schema::dropIfExists('expense_codes');
    }
};
