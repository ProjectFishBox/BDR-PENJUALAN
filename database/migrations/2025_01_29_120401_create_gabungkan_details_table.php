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
        Schema::create('gabungkan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_gabungkan');
            $table->string('kode_barang', 15);
            $table->string('merek', 25);
            $table->integer('jumlah');
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
        Schema::dropIfExists('gabungkan_detail');
    }
};
