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
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pembelian');
            $table->unsignedBigInteger('id_barang');
            $table->string('nama_barang');
            $table->string('merek');
            $table->integer('harga');
            $table->integer('jumlah')->nullable();
            $table->integer('subtotal')->nullable();
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
        Schema::dropIfExists('pembelian_detail');
    }
};
