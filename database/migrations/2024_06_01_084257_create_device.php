<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device', function (Blueprint $table) {
            $table->id('id_device');
            $table->uuid('uuid');
            $table->string('nama_device', 30);
            $table->string('device_id');
            $table->string('device_token');
            $table->boolean('actived');
            $table->timestamps();
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device');
    }
};
