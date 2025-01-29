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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lokasi');
            $table->date('tanggal');
            $table->string('no_nota');
            $table->unsignedBigInteger('id_pelanggan');
            $table->integer('total_penjualan');
            $table->integer('diskon_nota')->nullable();
            $table->integer('bayar');
            $table->integer('delete')->default(0);
            $table->integer('create_by');
            $table->integer('last_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
