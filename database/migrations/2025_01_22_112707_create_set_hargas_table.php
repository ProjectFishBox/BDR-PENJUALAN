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
        Schema::create('set_harga', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lokasi');
            $table->unsignedBigInteger('id_barang');
            $table->string('nama_barang', 50);
            $table->string('kode_barang', 15)->nullable();
            $table->string('merek', 25);
            $table->integer('harga')->nullable();
            $table->integer('untung');
            $table->integer('harga_jual');
            $table->enum('status', ['Tidak Aktif', 'Aktif']);
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
        Schema::dropIfExists('set_harga');
    }
};
