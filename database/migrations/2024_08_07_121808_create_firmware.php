<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firmware', function (Blueprint $table) {
            $table->id('id_firmware');
            $table->uuid('uuid');
            $table->string('name', 30);
            $table->string('description', 4000);
            $table->string('version', 10);
            $table->date('release_date');
            $table->string('file');
            $table->string('checksum');
            $table->enum('device', ['esp32', 'arduino']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firmware');
    }
};
