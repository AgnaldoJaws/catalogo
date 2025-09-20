<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('menu_sections')->nullOnDelete();

            // opcional: cardápio específico por filial
            $table->foreignId('business_loc_id')->nullable()->constrained('business_locations')->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('price_cents');         // preço em centavos
            $table->smallInteger('prep_time_minutes')->nullable();
            $table->json('tags')->nullable();               // ["veg","vegan","gluten-free"]
            $table->boolean('is_available')->default(true);
            $table->string('image_url')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['business_id','sort_order']);
            $table->index('is_available');
            $table->fullText(['name','description']);       // MySQL 8+
        });
    }
    public function down(): void {
        Schema::dropIfExists('menu_items');
    }
};
