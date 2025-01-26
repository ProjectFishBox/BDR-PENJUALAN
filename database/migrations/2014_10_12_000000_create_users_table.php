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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 25);
            $table->string('username', 25);
            $table->string('password', 100);
            $table->string('jabatan', 35)->nullable();
            $table->unsignedBigInteger('id_lokasi');
            $table->unsignedBigInteger('id_akses');
            $table->integer('delete')->default(0);
            $table->unsignedBigInteger('create_by');
            $table->unsignedBigInteger('last_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
