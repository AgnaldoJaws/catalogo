<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menu_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->string('group_name');     // "Tamanho", "Adicionais"
            $table->string('name');           // "Grande", "Bacon"
            $table->integer('price_delta_cents')->default(0);
            $table->smallInteger('max_select')->nullable(); // p/ grupos
            $table->timestamps();

            $table->index('item_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('menu_item_options');
    }
};
