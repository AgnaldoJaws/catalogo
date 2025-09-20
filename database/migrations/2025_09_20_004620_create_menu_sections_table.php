<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menu_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            // opcional: liberar cardápio específico da filial futuramente
            $table->foreignId('business_loc_id')->nullable()->constrained('business_locations')->cascadeOnDelete();
            $table->string('name');
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['business_id','sort_order']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('menu_sections');
    }
};
