<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('business_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();

            $table->string('name')->nullable();      // ex: "Batel", "Centro"
            $table->string('address')->nullable();

            // Coordenadas simples (sem tipo espacial)
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // Contatos/estado
            $table->string('whatsapp', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->tinyInteger('status')->default(1); // 1=online

            $table->timestamps();

            // Índices úteis p/ filtros
            $table->index(['city_id','status']);
            $table->index(['lat','lng']); // ajuda a filtrar/ordenar em queries por distância
        });
    }

    public function down(): void {
        Schema::dropIfExists('business_locations');
    }
};
