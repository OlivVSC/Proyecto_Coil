<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 15)->index();
            $table->string('titulo', 90);
            $table->text('resumen');
            $table->longText('contenido');
            $table->string('imagen', 120);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('publicaciones'); }
};
